<?php
namespace Themosis\Field\Fields;

use Themosis\Facades\Form;
use Themosis\Facades\View;

class InfiniteField extends FieldBuilder {

    /**
     * Number of registered rows.
     *
     * @var int
     */
    private $rows = 1;

    /**
     * Build an InfiniteField instance.
     *
     * @param array $properties
     */
    public function __construct(array $properties)
    {
        $this->properties = $properties;
        $this->setTitle();
        $this->setRows();
        $this->setLimit();
        $this->fieldType();
    }

    /**
     * Define the limit of rows we can add.
     *
     * @return void
     */
    private function setLimit()
    {
        $this['limit'] = isset($this['limit']) ? (int)$this['limit'] : 0;
    }

    /**
     * Set a default label title, display text if not defined.
     *
     * @return void
     */
    private function setTitle()
    {
        $this['title'] = isset($this['title']) ? ucfirst($this['title']) : ucfirst($this['name']);
    }

    /**
     * Set the number of rows to display.
     *
     * @return int
     */
    private function setRows()
    {
        $this->rows = (is_array($this['value']) && !empty($this['value'])) ? count($this['value']) : 1;
    }

    /**
     * Return the numbers of rows to display.
     *
     * @return int
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * Define the input type that handle the data.
     *
     * @return void
     */
    protected function fieldType()
    {
        $this->type = 'infinite';
    }

    /**
     * Handle the order/number HTML.
     *
     * @param int|string $number The order number to display.
     * @return string
     */
    private function order($number)
    {
        return '<span>'.$number.'</span>';
    }

    /**
     * Handle the main structure of the infinite field.
     *
     * @param string $method The method the field need to use to output their HTML.
     * @return string
     */
    private function infinite($method)
    {
        $output = '<div class="themosis-infinite-container">';
        $output .= '<table class="themosis-infinite"><tbody class="themosis-infinite-sortable" data-limit="'.$this['limit'].'">';

        // ROWs
        for($i = 1; $i <= $this->rows; $i++){

            // Check the limit.
            if(0 < $this['limit'] && $i > $this['limit']) break;

            // Specify the method each field will use to render.
            $output .= $this->row($i, $method);

        }

        $output .= '</tbody></table>';
        $output .= $this->info();
        $output .= $this->addButton();
        $output .= '</div>';

        return $output;
    }

    /**
     * Handle each row HTML.
     *
     * @param int $index The row index.
     * @param string $method The method name that handles field output.
     * @return string
     */
    private function row($index, $method)
    {
        $row = '<tr class="themosis-infinite-row">';
        $row .= $this->innerRow($index, $method);
        $row .= '</tr>';

        return $row;
    }

    /**
     * Handle the inner row HTML.
     *
     * @param int $index The row index.
     * @param string $method The method name that handles field output.
     * @return string
     */
    private function innerRow($index, $method)
    {
        $row = '<td class="themosis-infinite-order">'.$this->order($index).'</td>';
        $row .= '<td class="themosis-infinite-inner"><table><tbody>'.$this->fields($index, $this['fields'], $method).'</tbody></table></td>';
        $row .= '<td class="themosis-infinite-options"><span class="themosis-infinite-add"></span><span class="themosis-infinite-remove"></span></td>';

        return $row;
    }

    /**
     * Handle row inner fields HTML.
     *
     * @param int $index
     * @param array $fields The fields to repeat.
     * @param string $method The field output method to use.
     * @return string
     */
    private function fields($index, array $fields, $method)
    {
        $output = '';

        foreach($fields as $field){

            // Set the id attribute.
            $defaultId = $field['id'];
            $field['id'] = $index.'-'.$field['name'].'-id';

            // Grab the value if it exists.
            if(isset($this['value'][$index][$field['name']])){
                $field['value'] = $this['value'][$index][$field['name']];
            }

            // Set the name attribute.
            // Note: this completely change the name attribute. Do not write
            // any code that would need the default 'name' attribute below.
            $defaultName = $field['name'];
            $field['name'] = $this['name'].'['.$index.']['.$field['name'].']';

            // Render the field.
            $output.= $field->$method();

            // Reset Id, name and value.
            $field['id'] = $defaultId;
            $field['name'] = $defaultName;
            unset($field['value']);

        }

        return $output;
    }

    /**
     * Method that handle the field HTML code for
     * metabox output.
     *
     * @return string
     */
    public function metabox()
    {
        // Check rows number.
        $this->setRows();

        return View::make('metabox._themosisInfiniteField', array('field' => $this))->render();
    }
}