<?php
namespace frontend\controllers\auth;

use src\services\auth\PasswordResetService;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use src\forms\PasswordResetRequestForm;
use src\forms\ResetPasswordForm;

/**
 * Reset controller
 */
class ResetController extends Controller
{
    /**
     * @inheritdoc
     */
    private $passwordResetService;

    public function __construct($id, $module, PasswordResetService $passwordResetService, array $config = [])
    {
        $this->passwordResetService = $passwordResetService;
        parent::__construct($id, $module, $config);
    }

     /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $form = new PasswordResetRequestForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try
            {
                $this->passwordResetService->request($form);
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');
                return $this->goHome();
            }
            catch (\DomainException $e)
            {
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $form,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $this->passwordResetService->validateToken($token);
        } catch (\DomainException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        $form = new ResetPasswordForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try
            {
                $this->passwordResetService->resetPassword($token, $form);
                Yii::$app->session->setFlash('success', 'New password saved.');
                return $this->goHome();
            }
            catch(\DomainException $e)
            {
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }

        return $this->render('resetPassword', [
            'model' => $form,
        ]);
    }
}
