<?php
namespace Craft;

/**
 * TobiasAx - User Model
 */
class TobiasAx_UserModel extends TobiasAx_BaseModel
{
    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->Name;
    }

    /**
     * Defines attributes for this model
     * @return array
     */
    protected function defineAttributes()
    {
        return array_merge(parent::defineAttributes(), array(
            'Email' => AttributeType::String,
            'Enabled' => AttributeType::Bool,
            'Id' => AttributeType::String,
            'LastLogin' => AttributeType::DateTime,
            'Name' => AttributeType::String,
            'Password' => AttributeType::String,
            'PasswordExpires' => array(AttributeType::DateTime, 'default' => null),
            'Person' => array(AttributeType::ClassName => 'TobiasAx_PersonModel'),
            'Suspended' => AttributeType::Bool
        ));
    }
}
