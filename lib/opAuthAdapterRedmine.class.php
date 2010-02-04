<?php

/**
 * This file is part of the opAuthRedminePlugin package.
 * (c) 2010 Kousuke Ebihara (http://co3k.org/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

/**
 * opAuthAdapterRedmine will handle authentication for OpenPNE by Redmine account information
 *
 * @package    opAuthRedminePlugin
 * @subpackage user
 * @author     Kousuke Ebihara <ebihara@tejimaya.com>
 */
class opAuthAdapterRedmine extends opAuthAdapter
{
  protected
    $authModuleName = 'Redmine';

  public function configure()
  {
  }

  public function authenticate()
  {
    $result = parent::authenticate();

    if ($this->getAuthForm()->isValid()
      && $this->getAuthForm()->getValue('redmine_username')
      && !$this->getAuthForm()->getMember())
    {
      $member = Doctrine::getTable('Member')->createPre();
      $member->setConfig('redmine_username', $this->getAuthForm()->getValue('redmine_username'));
      $this->appendMemberInformationFromRedmine($member);

      $member->save();

      $result = $member->getId();
    }

    return $result;
  }

  public function getCurrentUrl()
  {
    return sfContext::getInstance()->getRequest()->getUri();
  }

  public function registerData($memberId, $form)
  {
    $member = Doctrine::getTable('Member')->find($memberId);
    if (!$member)
    {
      return false;
    }

    $member->setIsActive(true);
    return $member->save();
  }

  public function isRegisterBegin($member_id = null)
  {
    opActivateBehavior::disable();
    $member = Doctrine::getTable('Member')->find((int)$member_id);
    opActivateBehavior::enable();

    if (!$member || $member->getIsActive())
    {
      return false;
    }

    return true;
  }

  public function isRegisterFinish($member_id = null)
  {
    return false;
  }

  public function getRedmineConnection()
  {
    $manager = Doctrine_Manager::getInstance();

    if ($manager->contains('redmine'))
    {
      $conn = $manager->getConnection('redmine');
    }
    else
    {
      $dsn = $this->getAuthConfig('redmine_dsn');
      $conn = $manager->openConnection($dsn, 'redmine', false);
    }

    return $conn;
  }

  public function getRegisterEndAction()
  {
    return 'redmineAuth/registerEnd';
  }

  protected function appendMemberInformationFromRedmine($member)
  {
    $conn = $this->getRedmineConnection();

    $sql = 'SELECT * FROM users WHERE login = ?';
    $user = array_shift($conn->fetchAssoc($sql, array($member->getConfig('redmine_username'))));

    $member->setName($user['firstname'].' '.$user['lastname']);
    if (opToolkit::isMobileEmailAddress($user['mail']))
    {
      $member->setConfig('mobile_address', $user['mail']);
    }
    else
    {
      $member->setConfig('pc_address', $user['mail']);
    }

    return $member;
  }
}
