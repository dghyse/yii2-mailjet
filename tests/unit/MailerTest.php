<?php
/**
 * MailerTest.php
 *
 * PHP version 5.6+
 *
 * @author Philippe Gaultier <pgaultier@sweelix.net>
 * @copyright 2010-2017 Philippe Gaultier
 * @license http://www.sweelix.net/license license
 * @version XXX
 * @link http://www.sweelix.net
 * @package tests\unit
 */

namespace tests\unit;

use sweelix\mailjet\Mailer;

/**
 * Test node basic functions
 *
 * @author Philippe Gaultier <pgaultier@sweelix.net>
 * @copyright 2010-2017 Philippe Gaultier
 * @license http://www.sweelix.net/license license
 * @version XXX
 * @link http://www.sweelix.net
 * @package tests\unit
 * @since XXX
 */
class MailerTest extends TestCase
{

    public function setUp()
    {
        $this->mockApplication([
            'components' => [
                'email' => $this->createTestEmailComponent()
            ]
        ]);
    }

    protected function createTestEmailComponent()
    {
        $component = new Mailer();
        $component->apiKey = MAILJET_KEY;
        $component->apiSecret = MAILJET_SECRET;
        return $component;
    }

    public function testGetMailjetMailer()
    {
        $mailer = $this->createTestEmailComponent();
        $this->assertInstanceOf(Mailer::className(), $mailer);
    }
}
