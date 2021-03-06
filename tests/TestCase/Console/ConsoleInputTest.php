<?php
declare(strict_types=1);

/**
 * CakePHP :  Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP Project
 * @since         3.7.3
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace Cake\Test\TestCase\Console;

use Cake\Console\ConsoleInput;
use Cake\Console\Exception\ConsoleException;
use Cake\TestSuite\TestCase;

/**
 * ConsoleInput test.
 */
class ConsoleInputTest extends TestCase
{
    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->in = new ConsoleInput();
    }

    /**
     * Test dataAvailable method
     *
     * @return void
     */
    public function testDataAvailable()
    {
        $this->skipIf(
            DS === '\\',
            'Skip ConsoleInput tests on Windows as they fail on AppVeyor.'
        );

        try {
            $this->assertFalse($this->in->dataAvailable());
        } catch (ConsoleException $e) {
            $this->markTestSkipped(
                'stream_select raised an exception. ' .
                'This can happen when FD_SETSIZE is too small.'
            );
        }
    }
}
