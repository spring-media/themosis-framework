/** @jest-environment node */
import React from "react";
import ReactDOMServer from "react-dom/server";
import CollectionField from "./../../src/components/fields/CollectionField";
import Manager from "./../../src/components/Manager";

const makeField = (items: any[] = []) => ({
  name: "test_collection",
  value: items.map(i => i.id),
  label: {inner: "Collection"},
  attributes: {id: "collection-1"},
  validation: {rules: [], messages: []},
  options: {
    items,
    limit: null,
    type: "image",
    l10n: {add: "Add", remove: "Remove", title: "Select", button: "Use"},
    info: null,
  },
});

test("SSR: CollectionField renders without error", () => {
  const items = [
    {
      id: 1,
      attributes: {
        filename: "a.jpg",
        type: "image",
        subtype: "jpeg",
        sizes: {thumbnail: {url: "/a_t.jpg"}, full: {url: "/a.jpg"}},
        icon: "/a.jpg",
      },
    },
  ];

  const html = ReactDOMServer.renderToString(
    // @ts-ignore FieldProps
    <CollectionField field={makeField(items)} changeHandler={() => {
    }}/>
  );

  expect(typeof html).toBe("string");
  expect(html.length).toBeGreaterThan(0);
});


test('Collection field can be registered to components Manager', () => {
  const manager = new Manager();
  manager.addComponent('themosis.fields.collection', CollectionField);

  expect(manager.hasComponent('themosis.fields.collection')).toBeTruthy();
})
