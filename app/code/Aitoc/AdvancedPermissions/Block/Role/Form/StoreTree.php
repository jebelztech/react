<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */

/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Block\Role\Form;

use Magento\Framework\Data\Form\Element\CollectionFactory;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Escaper;

class StoreTree extends \Magento\Framework\Data\Form\Element\AbstractElement
{

    /**
     * StoreTree constructor.
     *
     * @param Factory $factoryElement
     * @param CollectionFactory $factoryCollection
     * @param Escaper $escaper
     * @param array $data
     */
    public function __construct(
        Factory $factoryElement,
        CollectionFactory $factoryCollection,
        Escaper $escaper,
        $data = []
    ) {
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
        $this->setType('checkbox');
        $this->setExtType('checkbox');
    }

    /**
     * Retrieve allow attributes
     *
     * @return string[]
     */
    public function getHtmlAttributes()
    {
        return [
            'type',
            'style',
            'onclick',
            'onchange',
            'disabled',
        ];
    }

    /**
     * Prepare value list
     *
     * @return array
     */
    protected function _prepareValues($results = null)
    {
        $results = null;
        $options = [];
        $values  = [];

        if ($this->getValues()) {
            if (!is_array($this->getValues())) {
                $options = [$this->getValues()];
            } else {
                $options = $this->getValues();
            }
        } elseif ($this->getOptions() && is_array($this->getOptions())) {
            $options = $this->getOptions();
        }

        foreach ($options as $value) {
            if (is_array($value)) {
                $values[] = ['label' => $value['label'], 'value' => $value['value'], 'type' => $value['type']];
                if (is_array($value['scopes'])) {
                    foreach ($value['scopes'] as $record) {
                        $values[] = [
                            'label'  => $record['label'],
                            'value'  => $record['value'],
                            'type'   => $record['type'],
                            'parent' => $record['parent']
                        ];
                    }
                }
            }
        }

        return $values;
    }

    /**
     * Retrieve HTML
     *
     * @return string
     */
    public function getElementHtml()
    {
        $values = $this->_prepareValues();

        if (!$values) {
            return '';
        }

        $html = '<div class=nested>';
        foreach ($values as $value) {
            $html .= $this->_optionToHtml($value);
        }
        $html .= '</div>' . $this->getAfterElementHtml();

        return $html;
    }

    /**
     * @param mixed $value
     *
     * @return string|void
     */
    public function getChecked($value)
    {
        if ($value['type'] == 'storeview') {
            if (in_array($value['value'], $this->getResults())) {
                return 'checked';
            }
        }
        if ($value['type'] == 'store') {
            if (in_array($value['value'], $this->getParents())) {
                return 'checked';
            }
        }

        return '';
    }

    /**
     * @param mixed $value
     *
     * @return string
     */
    public function getDisabled($value)
    {
        if ($disabled = $this->getData('disabled')) {
            if (!is_array($disabled)) {
                $disabled = [strval($disabled)];
            } else {
                foreach ($disabled as $k => $v) {
                    $disabled[$k] = strval($v);
                }
            }
            if (in_array(strval($value), $disabled)) {
                return 'disabled';
            }
        }

        return '';
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function getOnclick($value = '')
    {
        if ($onclick = $this->getData('onclick')) {
            return str_replace('$value', $value, $onclick);
        }

        return '';
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function getOnchange($value = '')
    {
        if ($onchange = $this->getData('onchange')) {
            return str_replace('$value', $value, $onchange);
        }

        return '';
    }

    /**
     *
     * Get names from type
     * @param $value
     *
     * @return string
     */
    public function getNames($value)
    {
        if ($value['type'] == 'storeview') {
            return 'storesview[' . $value['parent'] . '][]';
        } elseif ($value['type'] == 'store') {
            return 'store[]';
        }

        return '';
    }

    /**
     * Get classes from types
     *
     * @param $value
     *
     * @return string
     */
    public function getClasses($value)
    {
        if ($value['type'] == 'storeview') {
            return 'storesview';
        } elseif ($value['type'] == 'store') {
            return 'store';
        }

        return '';
    }

    /**
     * Get new classes from stores
     *
     * @param $value
     *
     * @return string
     */
    public function getNewClass($value)
    {
        if ($value['type'] == 'storeview') {
            return 'select_storesview';
        } elseif ($value['type'] == 'store') {
            return 'select_store';
        }

        return '';
    }

    /**
     *
     * @param array $option
     *
     * @return string
     */
    protected function _optionToHtml($option)
    {
        $id = $this->getNewClass($option) . '_' . $this->_escape($option['value']);

        $html =
            '<div class="field choice admin__field admin__field-option store_' . $option['type'] . '"><input id="' . $id . '"';
        if ($option['type'] == 'website') {
            $html = '<span><b>' . $option['label'] . '</b></span>';
        } else {
            foreach ($this->getHtmlAttributes() as $attribute) {
                if ($value = $this->getDataUsingMethod($attribute, $option['value'])) {
                    $html .=
                        ' ' . $attribute . '="' . $value . '" class="admin__control-checkbox ' . $this->getClasses($option) . '"';
                }
            }
            $html .= ' name="' . $this->getNames($option) . '"';
            $html .= ' data-type="' . $option['type'] . '"';
            if ($this->getChecked($option)) {
                $html .= ' checked="checked"';
            }
            if (isset($option['parent'])) {
                $html .= ' data-parent="' . $option['parent'] . '"';
            }
            $html .= ' value="' . $option['value'] . '" />' . ' <label for="' . $id
                . '" class="admin__field-label"><span>' . $option['label'] . '</span></label></div>' . "\n";
        }

        return $html;
    }
}
