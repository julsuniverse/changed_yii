<?php
namespace frontend\controllers\auth;

use src\services\auth\SignupService;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\AccessControl;
use src\forms\SignupForm;

/**
 * Signup controller
 */
class SignupController extends Controller
{
    /**
     * @inheritdoc
     */
    private $signupService;
    private $passwordResetService;
    private $contactService;
    private $authService;

    public function __construct($id, $module, SignupService $signupService, array $config = [])
    {
        $this->signupService = $signupService;
        parent::__construct($id, $module, $config);
    }

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $form = new SignupForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate())
        {
            try
            {
                $this->signupService->requestSignup($form);
                Yii::$app->session->setFlash('success', 'Check your email for further instructions');
                return $this->goHome();
            }
            catch( \DomainException $e)
            {
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }

        return $this->render('signup', [
            'model' => $form,
        ]);
    }

    public function actionConfirmEmail($token)
    {
        try
        {
            $user = $this->signupService->confirm($token);
            Yii::$app->session->setFlash('success', 'Your account was activated');
            Yii::$app->user->login($user);

            return $this->redirect(['index']);
        }
        catch(\DomainException $e)
        {
            throw new BadRequestHttpException($e->getMessage());

        }
    }

}
