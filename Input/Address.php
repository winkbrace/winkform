<?php namespace WinkBrace\WinkForm\Input;

class Address extends Input
{
    
    protected $type = 'address',
              $postcode,
              $houseNumber,
              $houseNumberExtension; // hidden input field with the name $name that will contain the YYYY-MM value
    
    
    /**
     * construct AddressInput object
     *
     * @param string $name
     */
    function __construct($name, $value = null)
    {
        $this->validator = new \WinkBrace\WinkForm\Validator();

        $this->name = $name;
        
        // create the text inputs
        // NOTE: names must be the same as the values for the jquery script
        $this->postcode = new Text('postcode', 'postcode');
        $this->houseNumber = new Text('huisnr', 'huisnr');
        $this->houseNumberExtension = new Text('toevoeging', 'toevoeging');
        
        // set the global style that will get copied down
        $this->setWidth(150)->addStyle('font-style:italic; color:#888;')->addClass('address');
    }
    
    /**
     * render the date range input fields
     */
    public function render()
    {
        // check result of validity checks of parameters passed to this Input element
        $this->checkValidity();

        // via casting we can pass all attributes that were set on AddressInput down to the DateInput fields
        $excludes = array('type', 'name','id','value','values','label','labels','selected','posted','required','invalidations');
        copySharedAttributes($this->postcode, $this, $excludes);
        copySharedAttributes($this->houseNumber, $this, $excludes);
        copySharedAttributes($this->houseNumberExtension, $this, $excludes);
        
        $this->postcode->setLabel($this->label);

        // render the date range input fields
        $output = $this->postcode->render()
                . $this->houseNumber->setWidth(50)->render()
                . $this->houseNumberExtension->render();

        $output .= $this->renderInvalidations();
        
        $output .= '<script>
                    $("input.address").focus(function() {
                        if ($(this).val() == $(this).attr("id")) {
                            $(this).val("").css({fontStyle:"normal", color:"black"});
                        }
                    });
                    </script>'."\n";
        
        // if setSelected has been used, remove the default values by triggering focus on the .address elements
        if (! empty($this->selected))
        {
            $output .= '<script>
                        $("input.address").trigger("focus");
                        </script>'."\n";
        }

        return $output;
    }
    
    /**
     * setSelected method that will set the child inputs values and makes the font normal and removes the default values
     * @param array $selected array(postcode, housenr, extension)
     * @param int $flag
     */
    public function setSelected($selected, $flag = 0)
    {
        if (empty($this->posted) || $this->isFlagSet($flag, self::INPUT_OVERRULE_POST))
        {
            // of flag is niet geset of wel geset maar dan moet POST empty zijn
            if (! $this->isFlagSet($flag, self::INPUT_SELECTED_INITIALLY_ONLY) || empty($_POST))
            {
                $this->selected = $selected;
                $this->removeStyle('font-style:italic; color:#888;');
                
                list($postcode, $housenumber, $extension) = $selected;
                $this->postcode->setSelected($postcode);
                $this->houseNumber->setSelected($housenumber);
                $this->houseNumberExtension->setSelected($extension);
            }
        }
        
        return $this;
    }
    
    /**
     * @return TextInput $postcode
     */
    public function getPostcode()
    {
        return $this->postcode;
    }
    
    /**
     * @return TextInput $house_number
     */
    public function getHouseNumber()
    {
        return $this->houseNumber;
    }
    
    /**
     * @return TextInput $house_number_extension
     */
    public function getHouseNumberExtension()
    {
        return $this->houseNumberExtension;
    }
    
    /**
     * (non-PHPdoc)
     * @see \WinkBrace\WinkForm\Input::isPosted()
     */
    public function isPosted()
    {
        return ($this->postcode->isPosted()
                && $this->houseNumber->isPosted()
                && $this->postcode->getPosted() != 'postcode'
                && $this->houseNumber->getPosted() != 'huisnr'
               );
    }
    
}
