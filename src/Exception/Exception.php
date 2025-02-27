<?php
declare(strict_types = 1);
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
namespace Phauthentic\Authorization\Exception;

use RuntimeException;
use Throwable;

/**
 * Exception
 */
class Exception extends RuntimeException
{
    /**
     * @var string
     */
    protected $messageTemplate = '';

    /**
     * Method to format the predefined message template and use it as message
     *
     * @param array $values Values
     * @return $this
     */
    public function setMessageVars(array $values = [])
    {
        $this->message = vsprintf($this->messageTemplate, $values);

        return $this;
    }
}
