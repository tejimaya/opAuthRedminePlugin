<?php

/**
 * This file is part of the opAuthRedminePlugin package.
 * (c) 2010 Kousuke Ebihara (http://co3k.org/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

/**
 * opAuthRegisterFormRedmine represents a form to register by Redmine account
 *
 * @package    opAuthRedminePlugin
 * @subpackage user
 * @author     Kousuke Ebihara <ebihara@tejimaya.com>
 */
class opAuthRegisterFormRedmine extends opAuthRegisterForm
{
  public function __construct($defaults = array(), $options = array(), $CSRFSecret = null)
  {
    parent::__construct($defaults, $options, $CSRFSecret);

    unset($this->configForm['password'], $this->configForm['password_confirm']);
  }

  public function doSave()
  {
    $this->getMember()->setIsActive(true);
    $this->getMember()->save();

    return $this->getMember();
  }
}
