<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         1.0.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace Authorization\Policy;

use Authorization\IdentityInterface;

/**
 * This interface should be implemented if a policy class needs to perform a
 * pre-authorization check before the action access check takes place.
 */
interface BeforePolicyInterface
{

    /**
     * Defines a pre-authorization check.
     *
     * @param \Authorization\IdentityInterface $identity Identity object.
     * @param mixed $resource The resource being operated on.
     * @return bool
     */
    public function before(IdentityInterface $identity, $resource);
}
