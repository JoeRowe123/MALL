<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2017/11/8
 * Time: 10:26
 */

namespace backend\models;


use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class User extends ActiveRecord implements IdentityInterface
{
    public $role;
    public function rules(){
        return [
            [['username','email','created_at'],'required'],
            [['username','email'],'unique'],
            ['email','email'],
            [['status','role'],'safe'],
            [['password_hash'], 'string', 'max' => 100],
        ];
    }
    public function attributeLabels(){
        return [
            'username'=>'用户名',
            'password_hash'=>'密码',
            'email'=>'邮箱',
            'status'=>'状态',
            'role'=>'角色',
        ];
    }
    //菜单栏
    public function getMenu(){
        $menuItems = [];
        //查询出所有一级菜单
        $menus = Menu::find()->where(['parent'=>0])->all();
        //将一级分类放入$menuItems中

        foreach ($menus as $menu){
            $items = [];
            //将所有二级菜单放入items中
            foreach ($menu->children as $child){
//                var_dump($child);die;
                //根据权限将二级菜单放入一级菜单中
                if (\Yii::$app->user->can($child->url)){
                    $items[] = ['label'=>$child->name,'url'=>'/'.$child->url.'.html'];
                }
            }
            $menuItem = ['label'=>$menu->name,'items'=>$items];
//            var_dump($menuItem);die;
            //根据二级菜单权限将一级菜单放入主菜单中
            if ($menuItem['items']!=[]){
                $menuItems[] = $menuItem;
            }
        }
        return $menuItems;
    }
    //自动更新时间当记录插入时，行为将当前时间戳赋值给 created_at 和 updated_at 属性；
    //当记录更新时，行为将当前时间戳赋值给 updated_at 属性。
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
            ],
        ];
    }

    /**
     * Finds an identity by the given ID.
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface the identity object that matches the given ID.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentity($id)
    {
        return self::findOne(['id'=>$id]);
    }

    /**
     * Finds an identity by the given token.
     * @param mixed $token the token to be looked for
     * @param mixed $type the type of the token. The value of this parameter depends on the implementation.
     * For example, [[\yii\filters\auth\HttpBearerAuth]] will set this parameter to be `yii\filters\auth\HttpBearerAuth`.
     * @return IdentityInterface the identity object that matches the given token.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        // TODO: Implement findIdentityByAccessToken() method.
    }

    /**
     * Returns an ID that can uniquely identify a user identity.
     * @return string|int an ID that uniquely identifies a user identity.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns a key that can be used to check the validity of a given identity ID.
     *
     * The key should be unique for each individual user, and should be persistent
     * so that it can be used to check the validity of the user identity.
     *
     * The space of such keys should be big enough to defeat potential identity attacks.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @return string a key that is used to check the validity of a given identity ID.
     * @see validateAuthKey()
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * Validates the given auth key.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @param string $authKey the given auth key
     * @return bool whether the given auth key is valid.
     * @see getAuthKey()
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }
}