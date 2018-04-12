<?php 
namespace app\task;

use think\Log;

use Nette\Mail\Message;
use Nette\Mail\SmtpMailer;

class EmailSend
{
    public function send($data)
    {
        // 根据消息中的数据进行实际的业务处理...
        try {
            $mail = new Message;
            $from = $data['from'].' <wmk223@163.com>';
            $mail->setFrom($from)
                ->addTo( $data['to'] )
                ->setSubject( $data['title'] )
                ->setBody( $data['contents'] );
            $mailer = new SmtpMailer([
                'host' => 'smtp.163.com',
                'username' => 'wmk223@163.com',
                'password' => 'Mn456123mN66', /* smtp独立密码 */
                    // 'secure' => 'ssl',
            ]);
            $rep = $mailer->send($mail);
            return true;
        } catch (\Exception $e) { //请继续投递任务
            $result  = \app\service\Jobs::push('emailSend', '\app\task\EmailSend', 'send', [$data]);
            return true;
        }
        
    }
}