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
namespace Authorization\Test\TestCase\Policy;

use Phauthentic\Authorization\Policy\StringResourceResolver;
use PHPUnit\Framework\TestCase;
use TestApp\Policy\ArticlePolicy;

/**
 * String Resource Resolver Test
 */
class StringResourceResolverTest extends TestCase
{
    /**
     * testGetPolicy
     *
     * @return void
     */
    public function testGetPolicy(): void
    {
        $resolver = new StringResourceResolver();
        $result = $resolver->getPolicy(ArticlePolicy::class);
        $this->assertInstanceOf(ArticlePolicy::class, $result);
    }
}
