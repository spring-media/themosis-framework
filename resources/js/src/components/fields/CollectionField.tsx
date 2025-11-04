import * as React from "react";
import {Description, Field} from "./common";
import Label from "../labels/Label";
import {getErrorsMessages, hasErrors, isRequired} from "../../helpers";
import Error from "../errors/Error";
import Button from "../buttons/Button";
import classNames from "classnames";
import {
  DndContext,
  closestCenter,
  PointerSensor,
  useSensor,
  useSensors,
  type DragEndEvent,
} from "@dnd-kit/core";
import {
  SortableContext,
  useSortable,
  rectSortingStrategy,
  arrayMove,
} from "@dnd-kit/sortable";
import {CSS} from "@dnd-kit/utilities";

type CollectionState = {
  frame: any;
  items: Array<any>;
  selected: Array<number>;
};

function SortableItem({
                        item,
                        thumbnail,
                        selected,
                        onToggle,
                        onRemove,
                      }: {
  item: any;
  thumbnail: string;
  selected: boolean;
  onToggle: (id: number) => void;
  onRemove: (id: number) => void;
}) {
  const {attributes, listeners, setNodeRef, transform, transition, isDragging} =
    useSortable({id: item.id});

  const style: React.CSSProperties = {
    transform: CSS.Transform.toString(transform),
    transition,
    opacity: isDragging ? 0.6 : 1,
    touchAction: "none",
  };

  return (
    <div
      ref={setNodeRef}
      style={style}
      className={classNames("themosis__collection__item", {selected})}
      onClick={() => onToggle(item.id)}
      data-testid={`row-${item.id}`}
    >
      <div
        className="themosis__collection__item__thumbnail"
        {...attributes}
        {...listeners}
        aria-label="drag-handle"
        data-testid={`handle-${item.id}`}
        style={{cursor: "grab"}}
      >
        <img src={thumbnail} alt={item.attributes.filename}/>
        <div className="themosis__collection__item__overlay">
          <p>{item.attributes.filename}</p>
        </div>
      </div>

      <Button
        className="themosis__collection__item__check"
        clickHandler={(e: React.MouseEvent) => {
          e.stopPropagation();
          onRemove(item.id);
        }}
      >
        <span className="icon"/>
      </Button>
    </div>
  );
}

function CollectionList({
                          items,
                          selectedIds,
                          onToggle,
                          onRemove,
                          onDragEnd,
                          distance = 12,
                        }: {
  items: any[];
  selectedIds: number[];
  onToggle: (id: number) => void;
  onRemove: (id: number) => void;
  onDragEnd: (e: DragEndEvent) => void;
  distance?: number;
}) {
  const sensors = useSensors(
    useSensor(PointerSensor, {activationConstraint: {distance}})
  );
  const ids = items.map((i) => i.id);

  return (
    <DndContext sensors={sensors} collisionDetection={closestCenter} onDragEnd={onDragEnd}>
      <SortableContext items={ids} strategy={rectSortingStrategy}>
        <div className="themosis__collection__list">
          {items.map((item: any) => {
            let thumbnail = item.attributes.icon;

            if (item.attributes.type === "image" && item.attributes.subtype !== "svg+xml") {
              const sizes = item.attributes.sizes;
              thumbnail =
                typeof sizes["thumbnail"] !== "undefined" ? sizes.thumbnail.url : sizes.full.url;
            }

            return (
              <SortableItem
                key={item.id}
                item={item}
                thumbnail={thumbnail}
                selected={selectedIds.includes(item.id)}
                onToggle={onToggle}
                onRemove={onRemove}
              />
            );
          })}
        </div>
      </SortableContext>
    </DndContext>
  );
}

class CollectionField extends React.Component<FieldProps, CollectionState> {
  constructor(props: FieldProps) {
    super(props);

    // Media library models are BackboneJS based. But when coming from our own
    // API, it is not. So we should avoid BackboneJS functions when
    // manipulating the models.
    this.state = {
      frame: null,
      items: props.field.options.items.length ? props.field.options.items : [],
      selected: [],
    };

    this.onDragEnd = this.onDragEnd.bind(this);
    this.openMediaLibrary = this.openMediaLibrary.bind(this);
    this.removeAll = this.removeAll.bind(this);
  }

  /**
   * Check if collection has items.
   *
   * @return boolean
   */
  hasItems() {
    return this.props.field.value.length;
  }

  /**
   * Open the media library.
   */
  openMediaLibrary() {
    this.state.frame.open();
  }

  /**
   * Handle media library selection.
   */
  select() {
    const collection = this.getSelection();
    let items = collection.models;

    // Limit selection and total items.
    if (this.props.field.options.limit) {
      let limit = this.props.field.options.limit - this.state.items.length,
        end = limit < 0 ? 0 : limit;

      items = items.slice(0, end);
    }

    // Filter selected items. Remove any selected items if already exists in the collection.
    items = items.filter((item: any) => {
      let ids = this.state.items.map((item: any) => {
        return item.id;
      });

      return -1 === ids.indexOf(item.id);
    });

    // Push new selection to existing list of items.
    items = this.state.items.slice().concat(items);

    // Update state.
    this.setState({
      items: items
    });

    // Send value.
    this.props.changeHandler(this.props.field.name, items.map((item: any) => {
      return item.id;
    }));
  }

  /**
   * Return media library selected items.
   *
   * @return Array
   */
  getSelection() {
    return this.state.frame.state('library').get('selection');
  }

  /**
   * Hightlight an item from the collection.
   *
   * @param id
   */
  toggleItem(id: number) {
    let selected = this.state.selected.slice(),
      item = selected.filter((itemID: number) => {
        return id === itemID;
      }).shift();

    if (item) {
      // Remove it from the selection.
      selected = selected.filter((itemID: number) => {
        return id !== itemID;
      });
    } else {
      // Add it to the selection.
      selected.push(id);
    }

    this.setState({
      selected: selected
    });
  }

  /**
   * Check if an item is selected.
   *
   * @param id
   */
  isSelected(id: number) {
    return this.state.selected.filter((itemID: number) => {
      return id === itemID;
    }).shift();
  }

  /**
   * Remove a selected item from the collection.
   *
   * @param id
   */
  removeItem(id: number) {
    let selected = this.state.selected.filter((itemID: number) => {
      return id !== itemID;
    });

    let items = this.state.items.filter((item: any) => {
      return id !== item.id;
    });

    this.setState({
      items: items,
      selected: selected
    });

    this.props.changeHandler(this.props.field.name, items.map((item: any) => {
      return item.id;
    }));
  }

  /**
   * Remove all selected items.
   */
  removeAll() {
    let items = this.state.items.filter((item: any) => {
      return -1 === this.state.selected.indexOf(item.id);
    });

    this.setState({
      items: items,
      selected: [],
    });

    this.props.changeHandler(this.props.field.name, items.map((item: any) => {
      return item.id;
    }));
  }

  /**
   * dnd-kit: Update state on drag end event.
   */
  onDragEnd(e: DragEndEvent) {
    const {active, over} = e;
    if (!over || active.id === over.id) return;

    const oldIndex = this.state.items.findIndex((i: any) => i.id === active.id);
    const newIndex = this.state.items.findIndex((i: any) => i.id === over.id);
    const items = arrayMove(this.state.items, oldIndex, newIndex);

    this.setState({
      items: items
    });

    this.props.changeHandler(
      this.props.field.name,
      items.map((i: any) => i.id)
    );
  }

  /**
   * Render the field.
   */
  render() {
    return (
      <Field field={this.props.field}>
        <div className="themosis__column__label">
          <Label
            required={isRequired(this.props.field)}
            for={this.props.field.attributes.id}
            text={this.props.field.label.inner}
          />
        </div>
        <div className="themosis__column__content">
          {this.renderCollection()}
          {hasErrors(this.props.field) && (
            <Error messages={getErrorsMessages(this.props.field)}/>
          )}
          {this.props.field.options.info && (
            <Description content={this.props.field.options.info}/>
          )}
        </div>
      </Field>
    );
  }

  /**
   * Render the collection.
   */
  renderCollection() {
    return (
      <div className="themosis__field__collection">
        <div className={classNames("themosis__collection", {show: this.hasItems()})}>
          {this.renderItems()}
        </div>
        <div className="themosis__collection__buttons">
          <Button
            className={classNames("button", "themosis__collection__button--add")}
            clickHandler={this.openMediaLibrary}
          >
            <span className="icon--media"/>
            {this.props.field.options.l10n.add}
          </Button>
          <Button
            className={classNames("themosis__collection__button--remove", {
              show: this.state.selected.length,
            })}
            clickHandler={this.removeAll}
          >
            {this.props.field.options.l10n.remove}
          </Button>
        </div>
      </div>
    );
  }

  /**
   * Render list and individual items.
   */
  renderItems() {
    return (
      <CollectionList
        items={this.state.items}
        selectedIds={this.state.selected}
        onToggle={(id) => this.toggleItem(id)}
        onRemove={(id) => this.removeItem(id)}
        onDragEnd={this.onDragEnd}
        distance={12}
      />
    );
  }

  componentDidMount() {
    const frame = wp.media({
      frame: 'select',
      multiple: true,
      title: this.props.field.options.l10n.title,
      button: {
        text: this.props.field.options.l10n.button,
        close: true
      },
      library: {
        type: this.props.field.options.type
      }
    });

    frame.on('select', this.select.bind(this));

    this.setState({
      frame: frame
    });
  }
}

export default CollectionField;
