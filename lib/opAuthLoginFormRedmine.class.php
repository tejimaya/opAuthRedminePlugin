<?php

/**
 * This file is part of the opAuthRedminePlugin package.
 * (c) 2010 Kousuke Ebihara (http://co3k.org/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

/**
 * opAuthLoginFormRedmine represents a form to login by one's Redmine account.
 *
 * @package    opAuthRedminePlugin
 * @subpackage user
 * @author     Kousuke Ebihara <ebihara@tejimaya.com>
 */
class opAuthLoginFormRedmine extends opAuthLoginForm
{
  public function configure()
  {
    $this
      ->setWidget('redmine_username', new sfWidgetFormInput())
      ->setValidator('redmine_username', new opValidatorString())

      ->setWidget('password', new sfWidgetFormInputPassword())
      ->setValidator('password', new opValidatorString())
    ;

    $this->mergePostValidator(new sfValidatorCallback(array(
      'callback' => array($this, 'validate'),
    )));

    parent::configure();
  }

  public function validate($validator, $values, $arguments = array())
  {
    $conn = $this->getAuthAdapter()->getRedmineConnection();

    $sql = 'SELECT id FROM users WHERE login = ? AND hashed_password = ? AND status = ?';
    $userId = $conn->fetchOne($sql, array($values['redmine_username'], sha1($values['password']), 1));
    if (!$userId)
    {
      throw new sfValidatorError($validator, 'Failed to redmine authentication');
    }

    $validator = new opAuthValidatorMemberConfig(array('config_name' => 'redmine_username'));
    $result = $validator->clean($values);

    // regenerate api token
    if (isset($result['member']))
    {
      $sql = 'SELECT id FROM tokens WHERE user_id = ? AND action = ?';
      $currentTokenId = $conn->fetchOne($sql, array($userId, 'api'));

      $newToken = sha1(time().$result['member']->id);

      if ($currentTokenId)
      {
        $conn->execute('UPDATE tokens SET value = ? WHERE id = ?' , array($newToken, $currentTokenId));
      }
      else
      {
        $conn->execute('INSERT INTO tokens VALUES (NULL, ?, ?, ?, NOW())', array($userId, 'api', $newToken));
      }

      $result['member']->setConfig('redmine_api_token', $newToken);
    }

    return $result;
  }
}
