<?php

/**
 * This file is part of the opAuthRedminePlugin package.
 * (c) 2010 Kousuke Ebihara (http://co3k.org/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

/**
 * ToRedmineRegistration action.
 *
 * @package    opAuthRedminePlugin
 * @subpackage action
 * @author     Kousuke Ebihara <ebihara@tejimaya.com>
 */
class ToRedmineRegistrationAction extends sfAction
{
 /**
  * Executes this action
  *
  * @param sfRequest $request A request object
  */
  public function execute($request)
  {
    $adapter = $this->getUser()->getAuthAdapter('Redmine');
    $this->redirect($adapter->getAuthConfig('register_account_url'));
  }
}
