<?php

/**
 * This file is part of the opAuthRedminePlugin package.
 * (c) 2010 Kousuke Ebihara (http://co3k.org/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

/**
 * RegisterEnd action.
 *
 * @package    opAuthRedminePlugin
 * @subpackage action
 * @author     Kousuke Ebihara <ebihara@tejimaya.com>
 */
class RegisterEndAction extends sfAction
{
 /**
  * Executes this action
  *
  * @param sfRequest $request A request object
  */
  public function execute($request)
  {
    $this->redirect('@homepage');
  }
}
