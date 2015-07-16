<?php
namespace TypeRocket\Fields;

use TypeRocket\Config as Config,
    TypeRocket\Buffer as Buffer,
    TypeRocket\Html\Generator as Generator;

class Repeater extends Field
{

    private $options;

    public function __construct()
    {
        $paths = Config::getPaths();
        wp_enqueue_script( 'typerocket-booyah', $paths['urls']['assets'] . '/js/booyah.js', array( 'jquery' ), '1.0',
            true );
        wp_enqueue_script( 'jquery-ui-sortable', array( 'jquery' ), '1.0', true );
    }

    public function getString()
    {

        $form = $this->getForm();
        $form->setDebugStatus( false );
        $settings = $this->getSettings();
        $fields   = $this->options;
        $name     = $this->getName();
        $html     = '';
        $utility  = new Buffer();

        // add controls
        if (isset( $settings['help'] )) {
            $help = "<div class=\"help\"> <p>{$settings['help']}</p> </div>";
            $this->removeSetting( 'help' );
        } else {
            $help = '';
        }

        // add button settings
        if (isset( $settings['add_button'] )) {
            $add_button_value = $settings['add_button'];
        } else {
            $add_button_value = "Add New";
        }

        // template for repeater groups
        $href = '#remove';
        $openContainer = '<div class="repeater-controls"><div class="collapse"></div><div class="move"></div><a href="' . $href . '" class="remove" title="remove"></a></div><div class="repeater-inputs">';
        $endContainer  = '</div>';

        $html .= '<div class="control-section tr-repeater">'; // start tr-repeater

        // setup repeater
        $cache_group = $form->getGroup();
        $cache_sub   = $form->getSub();

        $root_group = $this->getBrackets();
        $form->setGroup( $this->getBrackets() . "[{{ {$name} }}]" );

        // add controls (add, flip, clear all)
        $generator = new Generator();
        $default_null = $generator->newInput('hidden', $this->getAttribute('name'), null)->getString();

        $html .= "<div class=\"controls\"><div class=\"tr-repeater-button-add\"><input type=\"button\" value=\"{$add_button_value}\" class=\"button add\" /></div><div class=\"button-group\"><input type=\"button\" value=\"Flip\" class=\"flip button\" /><input type=\"button\" value=\"Contract\" class=\"tr_action_collapse button\"><input type=\"button\" value=\"Clear All\" class=\"clear button\" /></div>{$help}<div>{$default_null}</div></div>";

        $templateFields = str_replace(' name="', ' data-name="', $this->getTemplateFields());

        // render js template data
        $html .= "<div class=\"tr-repeater-group-template\" data-id=\"{$name}\">";
        $html .= $openContainer . $templateFields . $endContainer;
        $html .= '</div>';

        // render saved data
        $html .= '<div class="tr-repeater-fields">'; // start tr-repeater-fields
        $repeats = $this->getValue();
        if (is_array( $repeats )) {
            foreach ($repeats as $k => $array) {
                $html .= '<div class="tr-repeater-group">';
                $html .= $openContainer;
                $form->setGroup( $root_group . "[{$k}]" );
                $utility->startBuffer();
                $form->renderFields( $fields );
                $html .= $utility->indexBuffer( 'fields' )->getBuffer( 'fields' );
                $html .= $endContainer;
                $html .= '</div>';
            }
        }
        $html .= '</div>'; // end tr-repeater-fields
        $form->setGroup( $cache_group );
        $form->setSub( $cache_sub );
        $html .= '</div>'; // end tr-repeater
        $utility->cleanBuffer();

        return $html;
    }

    public function getTemplateFields()
    {
        $utility = new Buffer();

        $utility->startBuffer();
        $this->getForm()->setSetting('template', true)->renderFields( $this->options );
        $fields = $utility->indexBuffer( 'template' )->getBuffer( 'template' );
        $this->getForm()->removeSetting('template');

        return $fields;
    }

    public function setOption( $key, $value )
    {
        $this->options[ $key ] = $value;

        return $this;
    }

    public function setOptions( $options )
    {
        $this->options = $options;

        return $this;
    }

    public function getOption( $key, $default = null )
    {
        if ( ! array_key_exists( $key, $this->options ) ) {
            return $default;
        }

        return $this->options[ $key ];
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function removeOption( $key )
    {
        if ( array_key_exists( $key, $this->options ) ) {
            unset( $this->options[ $key ] );
        }

        return $this;
    }

}
