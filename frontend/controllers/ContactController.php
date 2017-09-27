<?php
namespace frontend\controllers;

use src\services\contact\ContactService;
use Yii;
use yii\web\Controller;
use src\forms\ContactForm;

/**
 * Contact controller
 */
class ContactController extends Controller
{
    /**
     * @inheritdoc
     */
    private $contactService;

    public function __construct($id, $module, ContactService $contactService, array $config = [])
    {
        $this->contactService = $contactService;
        parent::__construct($id, $module, $config);
    }

     /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $form = new ContactForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try
            {
                $this->contactService->sendEmail($form);
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            }
            catch(\DomainException $e)
            {
                Yii::$app->session->setFlash('error', $e->getMessage());
            }

            return $this->refresh();
        }

        return $this->render('contact', [
            'model' => $form,
        ]);
    }


}
