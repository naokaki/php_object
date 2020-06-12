<!-- <img src="img/mugennzyou.jpg" alt="" style="position: relative;width: 100%;">
<img src="img/koko.png" alt=""style="height:200px; width: 150px; position:absolute; top: 350px; left: 200px;">
<img src="img/konpeitou.jpg" alt="" style="height:200px; width: 300px; position:absolute; top: 100px; left: 800px;"> -->
<link rel="stylesheet" type="text/css" href="style.css">
<?php

ini_set('log_errors','on');//ログをとるか
ini_set('error_log','php.log');//ログの出力ファイルを指定
session_start();//セッションを使用

//食べ物格納用
$foods = array();
//抽象クラス（原初）

abstract class Origin{
  protected $name;
  protected $hp;
  protected $img;
  // protected $attackMin;
  // protected $attackMax;
  public function setName($str){
    $this->name = $str;
  }
  public function getName(){
    return $this->name;
  }
  public function setHp($num){
    $this->hp = $num;
  }
  public function getHp(){
    return $this->hp;
  }
  //ゲッター
  public function getImg(){
    return $this->img;
  }
  // public function attack($targetObj){
  //   $attackPoint = mt_rand($this->attackMin, $this->attackMax);
  //   if(!mt_rand(0,9)){ //10分の1の確率でクリティカル
  //     $attackPoint = $attackPoint * 1.5;
  //     $attackPoint = (int)$attackPoint;
  //     History::set($this->getName().'のクリティカルヒット!!');
  //   }
  //   $targetObj->setHp($targetObj->getHp()-$attackPoint);
  //   History::set($attackPoint.'ポイントのダメージ！');
  // }
}
//人（ねず子）クラス
class Human extends Origin{
  protected $originHumanHp;
  private $eatMin;
  private $eatMax;
  public function getOriginHumanHp(){
    return $this->originHumanHp;
  }
  public function __construct($name, $hp, $originHumanHp, $img, $eatMin, $eatMax){
    $this->name = $name;
    $this->hp = $hp;
    $this->originHumanHp = $originHumanHp;
    $this->img = $img;
    $this->eatMin = $eatMin;
    $this->eatMax = $eatMax;
  }
  //食べるを押した場合
  public function eat($targetObj){
    $eatAmount = mt_rand($this->eatMin, $this->eatMax);
    if(!mt_rand(0,4)){ //5分の1の確率で倍減る
      $eatAmount = $eatAmount * 2;
      $eatAmount = (int)$eatAmount;
      History::set('ラッキー!!'.$_SESSION['food']->getName().'がたくさん減った!!');
    }
    $targetObj->setHp($targetObj->getHp()-$eatAmount);
    History::set($eatAmount.'ポイントのダメージ');
  }
  //休むを押した場合
  public function rest($targetObj){
    if(!mt_rand(0,2)){ //3分の1の確率で休憩可　(少し回復)
      History::set($_SESSION['human']->getName().'は休憩した');
      $restPoint = mt_rand(30,80);
      History::set('少しおなかが空いてきた!');
      History::set($restPoint.'ポイント回復した');
      $targetObj->setHp($targetObj->getHp()+$restPoint);
    }else{
      History::set($_SESSION['human']->getName().'は休憩した');
      $restPoint = mt_rand(50,100);
      History::set('少し眠くなってきた');
      History::set($restPoint.'ポイントのダメージ');
      $targetObj->setHp($targetObj->getHp()-$restPoint);
    }
  }
}
//食べ物クラス
class Food extends Origin{
  //プロパティ
  protected $originFoodHp;
  protected $amountMin;
  protected $amountMax;
  public function getOriginFoodHp(){
    return $this->originFoodHp;
  }
  //コンストラクタ
  public function __construct($name, $hp, $originFoodHp, $img, $amountMin, $amountMax){
    $this->name = $name;
    $this->hp = $hp;
    $this->originFoodHp = $originFoodHp;
    $this->img = $img;
    $this->amountMin = $amountMin;
    $this->amountMax = $amountMax;
  }
  public function eaten($targetObj){
    $amount = mt_rand($this->amountMin, $this->amountMax);
    if(!mt_rand(0,4)){ //5分の1の確率で倍減る
      $amount = $amount * 2;
      $amount = (int)$amount;
      History::set('アンラッキー!?'.$_SESSION['human']->getName().'は大ダメージ!!');
    }
    $targetObj->setHp($targetObj->getHp()-$amount);
    History::set($amount.'ポイントのダメージ');
  }
}
//大きい食べ物クラス
class bigFood extends Food{
  private $manyAmount;
  function __construct($name, $hp, $img, $amount, $manyAmount){
    parent::__construct($name, $hp, $img, $amount);
    $this->manyAmount = $manyAmount;
  }
  public function manyAmount(){
    return $this->manyAmount;
  }
  public function eaten($targetObj){
    if(!mt_rand(0,4)){ //5分の1の確率で魔法攻撃
      History::set($this->name.'のいっぱい攻撃!!');
      $targetObj->setHp( $targetObj->getHp() - $this->manyAmount );
      History::set($this->magicAmount.'ポイントのダメージを受けた！');
    }else{
      function eaten($targetObj){
        if(!mt_rand(0,4)){ //5分の1の確率で倍減る
          $amount = $amount * 2;
          $amount = (int)$amount;
          History::set('ラッキー!!'.$this->getName().'がたくさん減った!!');
        }
        $targetObj->setHp($targetObj->getHp()-$amount);
        History::set($amount.'ポイントのダメージ');
      }
    }
  }
}
// interface HistoryInterface{
//   public function set();
//   public function clear();
// }
// 履歴管理クラス（インスタンス化して複数に増殖させる必要性がないクラスなので、staticにする）
class History{
  public static function set($str){
    // セッションhistoryが作られてなければ作る
    if(empty($_SESSION['history'])) $_SESSION['history'] = '';
    // 文字列をセッションhistoryへ格納
    $_SESSION['history'] .= $str.'<br>';
  }
  public static function clear(){
    unset($_SESSION['history']);
  }
}
//インスタンス生成
$humans[] = new Human('禰豆子', 500, 500, 'img/nezuko.jpg', 50, 100);
$humans[] = new Human('炭治郎', 600, 600, 'img/tanjiro.jpg', 70, 150);
$humans[] = new Human('善逸', 400, 400, 'img/zennitu.jpg', 10, 200);
$foods[] = new Food('金平糖', 100, 100, 'img/konpeitou.jpg', 1,10);
$foods[] = new Food('おはぎ', 200, 200, 'img/ohagi.jpg', 50,100);
$foods[] = new Food('ラムネ', 120, 120, 'img/ramune.jpg', 30,100);
$foods[] = new Food('スイカ', 250, 250, 'img/suika.jpg', 50,150);
$foods[] = new Food('鮭大根', 30, 30, 'img/syakedaikon.jpg', 50,70);
$foods[] = new Food('天ぷら', 180, 180, 'img/tenpura.jpg', 50,150);
$foods[] = new Food('うな重', 300, 300, 'img/unagi.jpg', 50,150);

function createFood(){
  global $foods;
  $food = $foods[mt_rand(0,6)];
  History::set($food->getName().'が現れた！');
  $_SESSION['food'] = $food;
}
function createHuman(){
  global $humans;
  $human = $humans[mt_rand(0,2)];
  History::set($human->getName().'の登場!!');
  $_SESSION['human'] = $human;
}
function changeHuman(){
  global $humans;
  $human = $humans[mt_rand(0,2)];
  History::set($human->getName().'に交代!!');
  $_SESSION['human'] = $human;
}
function init(){
  History::clear();
  History::set('食事の時間');
  $_SESSION['knockDownCount'] = 0;
  createHuman();
  createFood();
}

function gameOver(){
  header('Location: index.php?完食数='.$_SESSION['knockDownCount']);
  $_SESSION = array();
}

//1.post送信されていた場合
if(!empty($_POST)){
  $eatFlg = (!empty($_POST['eat'])) ? true : false;
  $restFlg = (!empty($_POST['rest'])) ? true : false;
  $nextFlg = (!empty($_POST['next'])) ? true : false;
  $changeFlg = (!empty($_POST['change'])) ? true : false;
  $startFlg = (!empty($_POST['start'])) ? true : false;
  error_log('POSTされた');

  if($startFlg){
    History::set('ゲームスタート');
    init();
  }else if($restFlg){
    //休むを押した場合
    History::clear();
      $_SESSION['human']->rest($_SESSION['human']);
    // 自分のhpが0以下になったらゲームオーバー
    if($_SESSION['human']->getHp() <= 0){
      gameOver();
    }else{
      // hpが0以下になったら、別の食べ物を出現させる
      if($_SESSION['food']->getHp() <= 0){
        History::set($_SESSION['food']->getName().'を完食した！');
        createFood();
        $_SESSION['knockDownCount'] = $_SESSION['knockDownCount']+1;
      }
    }
  }else if($nextFlg){
    //次を押した場合
    History::clear();
    History::set('次の食事');
    createFood();
  }else if($changeFlg){
    //交代を押した場合
    History::clear();
    changeHuman();
  }else if($eatFlg){
    //食べるを押した場合
    // 食べ物のHPが減る
    History::clear();
    History::set($_SESSION['human']->getName().'は'.$_SESSION['food']->getName().'を食べた');
    $_SESSION['human']->eat($_SESSION['food']);

    // 食べ物がが攻撃をする
    History::set($_SESSION['food']->getName().'の反撃！');
    $_SESSION['food']->eaten($_SESSION['human']);

    // 自分のhpが0以下になったらゲームオーバー
    if($_SESSION['human']->getHp() <= 0){
      gameOver();
    }else{
      // hpが0以下になったら、別の食べ物を出現させる
      if($_SESSION['food']->getHp() <= 0){
        History::clear();
        History::set($_SESSION['food']->getName().'を完食した！');
        createFood();
        $_SESSION['knockDownCount'] = $_SESSION['knockDownCount']+1;
      }
    }
  }
  $_POST = array();
}

 ?>
 <?php if(empty($_SESSION)){ ?>
   <div style="background: url(img/mugennzyou.jpg) no-repeat center top/cover; width: 1368px; height: 770px;">
    <div class="default-screen">
      <h2 class="title">ごはんを食べに行こう</h2>
      <form method="post">
        <input type="submit" class="gamestart" name="start" value="▶ゲームスタート!!">
        <div class="kamaboko-wrap">
          <img class="kamaboko" src="img/nezuko.jpg" alt="">
          <img class="kamaboko" src="img/tanjiro.jpg" alt="">
          <img class="kamaboko" src="img/zennitu.jpg" alt="">
        </div>
        <p class="pre-eat-count">
          前回完食した食材:<?php echo $_GET['完食数'];?>個
        </p>
      </form>
    </div>
  </div>
 <?php }else{ ?>
<body class="wrap">
  <div class="main　site-width">
    <div style="background: url(img/mugennzyou.jpg) no-repeat center top/cover; width: 1350px;">
      <div class="container">
        <div class="food-container">
          <div class="parameter">
            <div>
              <?php echo $_SESSION['food']->getName() ?>
            </div>
            <div class="meter">
              　　残り　<meter value="<?php echo $_SESSION['food']->getHp(); ?>" max="<?php echo $_SESSION['food']->getOriginFoodHp(); ?>"></meter>
            </div>
            <div class="number">
              <?php echo $_SESSION['food']->getHp(); ?>/<?php echo $_SESSION['food']->getOriginFoodHp(); ?>
            </div>
          </div>
          <div class="food-img">
            <img src="<?php echo $_SESSION['food']->getImg(); ?>" alt="">
          </div>
        </div>
        <div class="nezuko-container">
          <img src="<?php echo $_SESSION['human']->getImg(); ?>" alt="">
          <div class="parameter">
            <div>
              <?php echo $_SESSION['human']->getName(); ?>
            </div>
            <div class="meter">
              　　空腹ゲージ　<meter value="<?php echo $_SESSION['human']->getHp(); ?>" max="<?php echo $_SESSION['human']->getOriginHumanHp(); ?>"></meter>
            </div>
            <div class="number">
              <?php echo $_SESSION['human']->getHp(); ?>/<?php echo $_SESSION['human']->getOriginHumanHp(); ?>
            </div>
          </div>
        </div>
        <div class="food-counter">
          　　完食した食材の数： <?php echo $_SESSION['knockDownCount']; ?>
        </div>
        <div class="history-choice-container">
          <div class="history-container">
              <p class="history">
                <?php
                echo (!empty($_SESSION['history'])) ? $_SESSION['history'] : '';
                ?>
              </p>
          </div>
          <form class="choice-container" method="post">
            <input type="submit" class="input" name="eat" value="▶食べる">
            <input type="submit" class="input" name="rest" value="▶休む">
            <input type="submit" class="input" name="next" value="▶次!!">
            <input type="submit" class="input" name="change" value="▶交代!!">
          </form>
          <?php }?>
        </div>
      </div>
    </div>
  </div>
</body>
