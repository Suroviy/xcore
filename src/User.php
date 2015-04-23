<?php namespace Suroviy\Xcore;

use Validator,Hash;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract {

	use Authenticatable, CanResetPassword;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['name', 'email', 'password'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['password', 'remember_token','lasname'];
        
        public $validator = null;
        
        
        
        public function setPasswordAttribute($value)
        {
            $this->attributes['password'] = Hash::make($value);
        }
   
        public function save (array $option = array())
        {
            $id = ($this->id) ? ','.$this->id : null;

            $this->validator = Validator::make(
                array(
                    'email' => $this->attributes['email'],
                ),
                array(
                    'email' => 'unique:users,email'.$id,
                ),
                array(
                    'email.unique' => 'Пользователь с такой почтой уже зарегестрирован',
                )
            );       

            if ($this->validator->fails())
            {
                return false;
            }

            return parent::save($option);
        }

}
