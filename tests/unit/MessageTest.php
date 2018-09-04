<?php
/**
 * MessageTest.php
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
use sweelix\mailjet\Message;
use Yii;
use yii\base\InvalidParamException;
use yii\base\NotSupportedException;

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
class MessageTest extends TestCase
{

    public function setUp()
    {
        $this->mockApplication([
            'components' => [
                'email' => $this->createTestEmailComponent()
            ]
        ]);
    }

    public function tearDown()
    {
        parent::tearDown(); // TODO: Change the autogenerated stub
    }

    protected function createTestEmailComponent()
    {
        $component = new Mailer([
            'apiKey' => MAILJET_KEY,
            'apiSecret' => MAILJET_SECRET
        ]);
        return $component;
    }


    protected function createOtherEmailComponent()
    {
        $component = new Mailer();
        $component->apiKey = MAILJET_KEY;
        $component->apiSecret = MAILJET_SECRET;
        return $component;
    }

    /**
     * @return string test file path.
     */
    protected function getTestFilePath()
    {
        return Yii::getAlias('@test/runtime') . DIRECTORY_SEPARATOR . basename(get_class($this)) . '_' . getmypid();
    }

    /**
     * @return Message test message instance.
     */
    protected function createTestMessage()
    {
        return Yii::$app->get('mailer')->compose();
    }

    protected function createOtherTestMessage()
    {
        return $this->createOtherEmailComponent()->compose();
    }

    public function testMailerConfigured()
    {
        $mailComponent = $this->createTestEmailComponent();
        $this->assertNotNull($mailComponent->apiKey);
        $this->assertNotNull($mailComponent->apiSecret);
    }

    public function testGetMailjetMessage()
    {
        $message = new Message();
        $this->assertInstanceOf(Message::className(), $message);
    }

    public function testSetCharsetException()
    {
        $message = new Message();
        $this->expectException(NotSupportedException::class);
        $message->setCharset('utf-8');

    }

    public function testGetCharsetException()
    {
        $message = new Message();
        $charset = $message->getCharset();
        $this->assertEquals('utf-8', $charset);
    }

    public function testGettersSetters()
    {
        $message = new Message();
        $message->setFrom('test@email.com');
        $this->assertEquals('test@email.com', $message->getFrom());
        $message->setFrom(['test@email.com']);
        $this->assertEquals('test@email.com', $message->getFrom());
        $message->setFrom(['test@email.com' => 'Test User']);
        $this->assertEquals('Test User <test@email.com>', $message->getFrom());

        $message->setTo('test@email.com');
        $this->assertTrue(is_array($message->getTo()));
        $to = $message->getTo();
        $to = Message::convertEmails($to);
        $this->assertEquals('test@email.com', $to[0]['Email']);
        $this->assertFalse(isset($to[0]['Name']));
        $message->setTo(['test@email.com']);
        $to = $message->getTo();
        $to = Message::convertEmails($to);
        $this->assertEquals('test@email.com', $to[0]['Email']);
        $this->assertFalse(isset($to[0]['Name']));
        $message->setTo(['test@email.com' => 'Test User']);
        $to = $message->getTo();
        $to = Message::convertEmails($to);
        $this->assertEquals('test@email.com', $to[0]['Email']);
        $this->assertEquals('Test User', $to[0]['Name']);
        $message->setTo(['test@email.com' => 'Test, User']);
        $to = $message->getTo();
        $to = Message::convertEmails($to);
        $this->assertEquals('test@email.com', $to[0]['Email']);
        $this->assertEquals('Test, User', $to[0]['Name']);
        $message->setTo(['test@email.com' => 'Test User', 'test2@email.com']);
        $to = $message->getTo();
        $to = Message::convertEmails($to);
        $this->assertEquals('test@email.com', $to[0]['Email']);
        $this->assertEquals('Test User', $to[0]['Name']);
        $this->assertEquals('test2@email.com', $to[1]['Email']);
        $this->assertFalse(isset($to[1]['Name']));

        $message->setReplyTo('test@email.com');
        $this->assertEquals('test@email.com', $message->getReplyTo());
        $message->setReplyTo(['test@email.com']);
        $this->assertEquals('test@email.com', $message->getReplyTo());
        $message->setReplyTo(['test@email.com' => 'Test User']);
        $this->assertEquals('Test User <test@email.com>', $message->getReplyTo());
        $message->setReplyTo(['test@email.com' => 'Test, User']);
        $this->assertEquals('"Test, User" <test@email.com>', $message->getReplyTo());
        $message->setReplyTo(['test@email.com' => 'Test User', 'test2@email.com']);
        $this->assertEquals('Test User <test@email.com>, test2@email.com', $message->getReplyTo());

        $message->setCc('test@email.com');
        $this->assertTrue(is_array($message->getCc()));
        $this->assertEquals('test@email.com', Message::stringifyEmails($message->getCc()));
        $message->setCc(['test@email.com']);
        $this->assertEquals('test@email.com', Message::stringifyEmails($message->getCc()));
        $message->setCc(['test@email.com' => 'Test User']);
        $this->assertEquals('Test User <test@email.com>', Message::stringifyEmails($message->getCc()));
        $message->setCc(['test@email.com' => 'Test, User']);
        $this->assertEquals('"Test, User" <test@email.com>', Message::stringifyEmails($message->getCc()));
        $message->setCc(['test@email.com' => 'Test User', 'test2@email.com']);
        $this->assertEquals('Test User <test@email.com>, test2@email.com', Message::stringifyEmails($message->getCc()));

        $message->setBcc('test@email.com');
        $this->assertTrue(is_array($message->getBcc()));
        $this->assertEquals('test@email.com', Message::stringifyEmails($message->getBcc()));
        $message->setBcc(['test@email.com']);
        $this->assertEquals('test@email.com', Message::stringifyEmails($message->getBcc()));
        $message->setBcc(['test@email.com' => 'Test User']);
        $this->assertEquals('Test User <test@email.com>', Message::stringifyEmails($message->getBcc()));
        $message->setBcc(['test@email.com' => 'Test, User']);
        $this->assertEquals('"Test, User" <test@email.com>', Message::stringifyEmails($message->getBcc()));
        $message->setBcc(['test@email.com' => 'Test User', 'test2@email.com']);
        $this->assertEquals('Test User <test@email.com>, test2@email.com', Message::stringifyEmails($message->getBcc()));

        $message->setSubject('Subject');
        $this->assertEquals('Subject', $message->getSubject());

        $message->setTextBody('Body stuff');
        $this->assertEquals('Body stuff', $message->getTextBody());

        $message->setHtmlBody('Body stuff');
        $this->assertEquals('Body stuff', $message->getHtmlBody());

        $message->setTag('tag');
        $this->assertEquals('tag', $message->getTag());

        $this->assertEquals('account_default', $message->getTrackOpens());
        $message->setTrackOpens('enabled');
        $this->assertEquals('enabled', $message->getTrackOpens());

        $message->setTemplateId(218932);
        $this->assertEquals(218932, $message->getTemplateId());

        $message->setTemplateModel(['a' => 'b']);
        $this->assertArrayHasKey('a', $message->getTemplateModel());
        $this->assertEquals('b', $message->getTemplateModel()['a']);

        $this->assertTrue($message->getInlineCss());
        $message->setInlineCss(false);
        $this->assertFalse($message->getInlineCss());

        $this->assertEmpty($message->getHeaders());
        $message->addHeader('X-Header', 'test');
        $this->assertArrayHasKey('X-Header', $message->getHeaders());
        $message->addHeader('X-Secondary', 'test');
        $this->assertArrayHasKey('X-Header', $message->getHeaders());
        $this->assertArrayHasKey('X-Secondary', $message->getHeaders());

        $this->assertNull($message->getAttachments());
        $message->attach(__FILE__, ['fileName' => 'file.php', 'contentType' => 'text/plain']);
        $this->assertEquals('file.php', $message->getAttachments()[0]['Filename']);
        $this->assertEquals('text/plain', $message->getAttachments()[0]['ContentType']);
        $this->assertEquals(base64_encode(file_get_contents(__FILE__)), $message->getAttachments()[0]['Base64Content']);

        $message->attach(__FILE__);
        $this->assertEquals('MessageTest.php', $message->getAttachments()[1]['Filename']);
        $this->assertEquals('application/octet-stream', $message->getAttachments()[1]['ContentType']);
        $this->assertEquals(base64_encode(file_get_contents(__FILE__)), $message->getAttachments()[1]['Base64Content']);

        $message->attachContent('plop', ['fileName' => 'file.php', 'contentType' => 'text/plain']);
        $this->assertEquals('file.php', $message->getAttachments()[2]['Filename']);
        $this->assertEquals('text/plain', $message->getAttachments()[2]['ContentType']);
        $this->assertEquals(base64_encode('plop'), $message->getAttachments()[2]['Base64Content']);

        $message->attachContent('plop', ['fileName' => 'file.php']);
        $this->assertEquals('file.php', $message->getAttachments()[3]['Filename']);
        $this->assertEquals('application/octet-stream', $message->getAttachments()[3]['ContentType']);
        $this->assertEquals(base64_encode('plop'), $message->getAttachments()[3]['Base64Content']);

        $cid = $message->embed(__FILE__, ['fileName' => 'file.php', 'contentType' => 'text/plain']);
        $this->assertEquals('file.php', $message->getInlinedAttachments()[4]['Filename']);
        $this->assertEquals('text/plain', $message->getInlinedAttachments()[4]['ContentType']);
        $this->assertEquals(base64_encode(file_get_contents(__FILE__)), $message->getInlinedAttachments()[4]['Base64Content']);
        $this->assertEquals($cid, $message->getInlinedAttachments()[4]['ContentID']);

        $cid = $message->embed(__FILE__);
        $this->assertEquals('MessageTest.php', $message->getInlinedAttachments()[5]['Filename']);
        $this->assertEquals('application/octet-stream', $message->getInlinedAttachments()[5]['ContentType']);
        $this->assertEquals(base64_encode(file_get_contents(__FILE__)), $message->getInlinedAttachments()[5]['Base64Content']);
        $this->assertEquals($cid, $message->getInlinedAttachments()[5]['ContentID']);

        $cid = $message->embedContent('plop', ['fileName' => 'file.php', 'contentType' => 'text/plain']);
        $this->assertEquals('file.php', $message->getInlinedAttachments()[6]['Filename']);
        $this->assertEquals('text/plain', $message->getInlinedAttachments()[6]['ContentType']);
        $this->assertEquals(base64_encode('plop'), $message->getInlinedAttachments()[6]['Base64Content']);
        $this->assertEquals($cid, $message->getInlinedAttachments()[6]['ContentID']);

        $cid = $message->embedContent('plop', ['fileName' => 'file.php']);
        $this->assertEquals('file.php', $message->getInlinedAttachments()[7]['Filename']);
        $this->assertEquals('application/octet-stream', $message->getInlinedAttachments()[7]['ContentType']);
        $this->assertEquals(base64_encode('plop'), $message->getInlinedAttachments()[7]['Base64Content']);
        $this->assertEquals($cid, $message->getInlinedAttachments()[7]['ContentID']);
    }

    public function testAttachException()
    {
        $message = new Message();

        $this->expectException(InvalidParamException::class);
        $message->attachContent('plop');
    }

    public function testEmbedException()
    {
        $message = new Message();

        $this->expectException(InvalidParamException::class);
        $message->embedContent('plop');
    }


    public function testBasicSend()
    {
        // allow disabling real tests
        if (MAILJET_TEST_SEND === true) {
            $message = $this->createTestMessage();
            $message->setFrom(MAILJET_FROM);
            // $message->setSender(MAILJET_SENDER);
            $message->setTo(MAILJET_TO);
            $message->setSubject('Yii MailJet test message');
            $message->setTextBody('Yii MailJet test body');
            $this->assertTrue($message->send());
        }
    }

    public function testParametersSend()
    {
        if (MAILJET_TEST_SEND === true) {
            $message = $this->createOtherTestMessage();
            $message->setFrom(MAILJET_FROM);
            $message->setTo(MAILJET_TO);
            $message->setSubject('Yii MailJet test message');
            $message->setTextBody('Yii MailJet test body');
            $this->assertTrue($message->send());
        }

    }

    public function testTemplateSend()
    {
        // allow disabling real tests
        if (MAILJET_TEST_SEND === true) {
            $message = $this->createTestMessage();
            $message->setFrom(MAILJET_FROM)
                ->setTo(MAILJET_TO)
                ->setTemplateId(MAILJET_TEMPLATE)
                ->setTemplateModel([
                    'templateName' => 'test',
                    'userName' => 'Mr test'
                ]);
            $this->assertTrue($message->send());
        }
    }
}
