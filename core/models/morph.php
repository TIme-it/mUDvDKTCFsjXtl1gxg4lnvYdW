<?php
mb_internal_encoding('utf-8');
	class lastName {
    var $exceptions = array(
        "	дюма,тома,дега,люка,ферма,гамарра,петипа . . . . .",
        '	гусь,ремень,камень,онук,богода,нечипас,долгопалец,маненок,рева,кива . . . . .',
        '	вий,сой,цой,хой -я -ю -я -ем -е'
    );
    var $suffixes = array(
        'f	б,в,г,д,ж,з,й,к,л,м,н,п,р,с,т,ф,х,ц,ч,ш,щ,ъ,ь . . . . .',
        'f	ска,цка  -ой -ой -ую -ой -ой',
        'f	ая       --ой --ой --ую --ой --ой',
        '	ская     --ой --ой --ую --ой --ой',
        'f	на       -ой -ой -у -ой -ой',

        '	иной -я -ю -я -ем -е',
        '	уй   -я -ю -я -ем -е',
        '	ца   -ы -е -у -ей -е',

        '	рих  а у а ом е',

        '	ия                      . . . . .',
        '	иа,аа,оа,уа,ыа,еа,юа,эа . . . . .',
        '	их,ых                   . . . . .',
        '	о,е,э,и,ы,у,ю           . . . . .',

        '	ова,ева            -ой -ой -у -ой -ой',
        '	га,ка,ха,ча,ща,жа  -и -е -у -ой -е',
        '	ца  -и -е -у -ей -е',
        '	а   -ы -е -у -ой -е',

        '	ь   -я -ю -я -ем -е',

        '	ия  -и -и -ю -ей -и',
        '	я   -и -е -ю -ей -е',
        '	ей  -я -ю -я -ем -е',

        '	ян,ан,йн   а у а ом е',

        '	ынец,обец  --ца --цу --ца --цем --це',
        '	онец,овец  --ца --цу --ца --цом --це',

        '	ц,ч,ш,щ   а у а ем е',

        '	ай  -я -ю -я -ем -е',
        '	ой  -го -му -го --им -м',
        '	ах,ив   а у а ом е',

        '	ший,щий,жий,ний  --его --ему --его -м --ем',
        '	кий,ый   --ого --ому --ого -м --ом',
        '	ий       -я -ю -я -ем -и',

        '	ок  --ка --ку --ка --ком --ке',
        '	ец  --ца --цу --ца --цом --це',

        '	в,н   а у а ым е',
        '	б,г,д,ж,з,к,л,м,п,р,с,т,ф,х   а у а ом е'
    );
}

class firstName {
    var $exceptions = array (
        '	лев    --ьва --ьву --ьва --ьвом --ьве',
        '	павел  --ла  --лу  --ла  --лом  --ле',
        'm	шота   . . . . .',
        'f	рашель,нинель,николь,габриэль,даниэль   . . . . .'
    );
    var $suffixes = array(
        '	е,ё,и,о,у,ы,э,ю   . . . . .',
        'f	б,в,г,д,ж,з,й,к,л,м,н,п,р,с,т,ф,х,ц,ч,ш,щ,ъ   . . . . .',

        'f	ь   -и -и . ю -и',
        'm	ь   -я -ю -я -ем -е',

        '	га,ка,ха,ча,ща,жа  -и -е -у -ой -е',
        '	а   -ы -е -у -ой -е',
        '	ия  -и -и -ю -ей -и',
        '	я   -и -е -ю -ей -е',
        '	ей  -я -ю -я -ем -е',
        '	ий  -я -ю -я -ем -и',
        '	й   -я -ю -я -ем -е',
        '	б,в,г,д,ж,з,к,л,м,н,п,р,с,т,ф,х,ц,ч	 а у а ом е'
    );
}

class middleName {
	var $exceptions = array();
    var $suffixes = array (
        '	ич   а  у  а  ем  е',
        '	на  -ы -е -у -ой -е'
    );
}
        
class Rules {
    var $lastName, $firstName, $middleName;
    function Rules(){
        $this->lastName = new lastName();
        $this->firstName = new firstName();
        $this->middleName = new middleName();
    }
}

class morph extends libs_controller {
    var $sexM = 'm';
    var $sexF = 'f';
    var $gcaseIm =  'nominative';      var $gcaseNom = 'nominative';      // именительный
    var $gcaseRod = 'genitive';        var $gcaseGen = 'genitive';        // родительный
    var $gcaseDat = 'dative';                                       // дательный
    var $gcaseVin = 'accusative';      var $gcaseAcc = 'accusative';      // винительный
    var $gcaseTvor = 'instrumentative';var $gcaseIns = 'instrumentative'; // творительный
    var $gcasePred = 'prepositional';  var $gcasePos = 'prepositional';   // предложный
    
    var $fullNameSurnameLast = false;
    var $ln = '', $fn = '', $mn = '', $sex = '';

    var $rules;
    var $initialized = false;

    function init(){
        if ( $this -> initialized ) { 
            return;
        }
        $this->rules = new rules();
        $this->prepareRules();
        $this -> initialized = true;
    }
    public function message_sex($string = false, $sex = 0){
        mb_internal_encoding('UTF-8');
        if(!empty($string)){
            switch ($sex) {
                case '2':
                    $var_start_pos = mb_strpos($string, '{?#s}');
                    $var_end_pos = mb_strpos($string, '{/?#s}');  
                    if((!empty( $var_end_pos)) &&(!empty( $var_start_pos))){
                        $string = str_replace(mb_substr($string,$var_start_pos,mb_strpos($string, '{/?#s}',$var_start_pos)-$var_start_pos+6), 
                        mb_substr($string, $var_start_pos+5,mb_strpos($string, '#',$var_start_pos+5)-$var_start_pos-5),
                        $string);
                    }               
                   
                    break;
                
                default:
                   $var_start_pos = mb_strpos($string, '{?#s}');
                   $var_end_pos = mb_strpos($string, '{/?#s}');  
                    if((!empty( $var_end_pos)) &&(!empty( $var_start_pos))){                 
                        $string = str_replace(mb_substr($string,$var_start_pos,mb_strpos($string, '{/?#s}',$var_start_pos)-$var_start_pos+6), 
                        mb_substr($string, mb_strpos($string, '#',$var_start_pos+5)+1,mb_strpos($string, '{/?#s}',$var_start_pos)-mb_strpos($string, '#',$var_start_pos+5)-1),
                        $string);
                    }
                    break;
            }
        }
        else{
            return false;
        }
        return $string;

    }
    function morphi ($lastName, $firstName = NULL, $middleName = NULL, $sex = NULL) {        
        $this->init();
        if (!isset($firstName)) {            
            preg_match("/^\s*(\S+)(\s+(\S+)(\s+(\S+))?)?\s*$/", $lastName, $m);            
            // if(!$m) exit("Cannot parse supplied name");
            if(!$m) return;
            if($m[5] && preg_match("/(ич|на)$/",$m[3]) && !preg_match("/(ич|на)$/",$m[5])) {
                // Иван Петрович Сидоров
                $lastName = $m[5];
                $firstName = $m[1];
                $middleName = $m[3];
                $this -> fullNameSurnameLast = true;
            } else {
                // Сидоров Иван Петрович
                $lastName = $m[1];
                $firstName = $m[3];
                $middleName = $m[5];
            }
        }
        $this -> ln = $lastName;
        if (isset($firstName)) $this -> fn = $firstName;
        else $this -> fn = '';
        if (isset($middleName)) $this -> mn = $middleName;
        else $this -> mn = '';
        if (isset($sex)) $this -> sex = $sex;
        else $this -> sex = $this -> getSex();               
        return ;
    }

    // версия номер один (некорректно работает с UTF8)
    function get_all($lastName, $firstName = NULL, $middleName = NULL, $sex = NULL){        
        $this->morphi($lastName, $firstName, $middleName, $sex);
        $obj = array(
            'i' =>  $this->fullName($this->gcaseNom),
            'r' =>  $this->fullName($this->gcaseGen),
            'd' =>  $this->fullName($this->gcaseDat),
            'v' =>  $this->fullName($this->gcaseVin),
            't' =>  $this->fullName($this->gcaseTvor),
            'p' =>  $this->fullName($this->gcasePred)            
        );
        // preg_replace('/$x5D$/', '', $obj);
        return $obj;
    }

    // версия номер два (яндекс.морфология)
    function get_yandex_morph($lastName, $firstName = NULL, $middleName = NULL){
        $name = $lastName.' '.$firstName.' '.$middleName;
        $url = 'http://export.yandex.ru/inflect.xml?name='.urlencode($name);
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Opera/9.80 (Windows NT 6.1; U; ru) Presto/2.6.30 Version/10.61');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl);
        curl_close($curl);
        $cases = array();
        preg_match_all('#\<inflection\s+case\=\"([0-9]+)\"\>(.*?)\<\/inflection\>#si', $result, $m);
        if ( count($m[0]) ) {
            foreach ($m[1] as $i => &$id) {
                $cases[(int)$id] = $m[2][$i];
            } unset ($id);
        } else return null;
        if (count($cases) > 1) return $cases;
        else return false;
    }

    // версия номер три (можно попробовать доработать)
    //склоняет имя, учитывая пол $s(1 - мужской, 0 - женский)
    //падеж $p (1 - именительный; 2 - родительный; 3 - дательный; 4 - винительный; 5 - творительный; 6 - предложный)
    function smart_name($lastName, $firstName = NULL, $middleName = NULL, $s, $p){
        $name = $lastName.' '.$firstName.' '.$middleName;
        $f_end1 = array('ла','ка','ша','ра','ня','тя','на','га','ля','та','да','са','ия','дя','ся','ья','вь','ва','оя','за','ая','ма','фа','йа');
        $f_end2 = array('лы','ки','ши','ры','ни','ти','ны','ги','ли','ты','ды','сы','ии','ди','си','ьи','ви','вы','ои','зы','аи','мы','фы','йи');
        $f_end3 = array('ле','ке','ше','ре','не','те','не','ге','ле','те','де','се','ии','де','се','ье','ви','ве','ое','зе','ае','ме','фе','йе');
        $f_end4 = array('лу','ку','шу','ру','ню','тю','ну','гу','лю','ту','ду','су','ию','дю','сю','ью','вь','ву','ою','зу','аю','му','фу','йю');
        $f_end5 = array('лой','кой','шей','рой','ней','тей','ной','гой','лей','той','дой','сой','ией','дей','сей','ьей','вью','вой','оей','зой','аей','мой','фой','йей');
        $f_end6 = array('ле','ке','ше','ре','не','те','не','ге','ле','те','де','се','ии','де','се','ье','ви','ве','ое','зе','ае','ме','фе','йе');
        $m_end1 = array('др','ма','ас','ша','ей','ня','ик','ва','рь','ля','лл','им','кс','тя','ад','ан','ат','ий','ха','ём','ем','ян','ис','ай','ир','ав','эн','ен','ег','ил','еб','ев','ам','он','ид','рп','ин','ор','ст','от','иф','кс','ар','та','нт','рх','тр','ум','ов','рк','ьф','ед','ьд','кт','ьм','яс','их','ет','ия','ья','ак','рт','рл');
        $m_end2 = array('дра','мы','аса','ши','ея','ни','ика','вы','ря','ли','лла','има','кса','ти','ада','ана','ата','ия','хи','ёма','ема','яна','иса','ая','ира','ава','эна','ена','ега','ила','еба','ьва','ама','она','ида','рпа','ина','ора','ста','ота','ифа','кса','ара','ты','нта','рха','тра','ума','ова','рка','ьфа','еда','ьда','кта','ьма','яса','иха','ета','ия','ьи','ака','рта','рла');
        $m_end3 = array('ме','асу','ше','ею','не','ику','ве','рю','ле','ллу','иму','ксу','те','аду','ану','ату','ию','хе','ёму','ему','яну','ису','аю','иру','аву','эну','ену','егу','илу','ебу','ьву','аму','ону','иду','рпу','ину','ору','сту','оту','ифу','ксу','ару','те','нту','рху','тру','уму','ову','рку','ьфу','еду','ьду','кту','ьму','ясу','иху','ету','ию','ье','аку','рту','рлу');
        $m_end4 = array('му','аса','шу','ея','ню','ика','ву','ря','лю','лла','има','кса','тю','ада','ана','ата','ия','ху','ёму','ему','яна','иса','ая','ира','ава','эна','ена','ега','ила','еба','ьва','ама','она','ида','рпа','ина','ора','ста','ота','ифа','кса','ара','ту','нта','рха','тра','ума','ова','рка','ьфа','еда','ьда','кта','ьма','яса','иха','ета','ия','ью','ака','рта','рла');
        $m_end5 = array('мой','аом','шей','еем','ней','иком','вой','рем','лей','ллом','има','ксом','тей','адом','аном','атом','ием','хой','ёмом','емом','яном','исом','аем','иром','авом','эном','еном','егом','илом','ебом','ьвом','амом','оном','идом','рпом','ином','ором','стом','отом','ифом','ксом','аром','той','нтом','рхом','тром','умом','овом','рком','ьфом','едом','ьдом','ктом','ьмом','ясом','ихом','етом','ием','ьёй','аком','ртом','рлом');
        $m_end6 = array('ме','асе','ше','ее','не','ике','ве','ре','ле','лле','име','ксе','те','аде','ане','ате','ии','хе','ёме','еме','яне','исе','ае','ире','аве','эне','ене','еге','иле','ебе','ьве','аме','оне','иде','рпе','ине','оре','сте','оте','ифе','ксе','аре','те','нте','рхе','тре','уме','ове','рке','ьфе','еде','ьде','кте','ьме','ясе','ихе','ете','ие','ье','аке','рте','рле');
        $name = strtolower($name);
        $num = strlen($name);
        $r = ucfirst($name);
        $lch = $name{$num-2}.$name{$num-1};
        if($s == 0) {
            $amount = count($f_end1);
            if(in_array($lch, $f_end1)) {
            if($p == 2) {
                for($i=0;$i<$amount;$i++)
                    ($lch==$f_end1[$i])?$ch=$f_end2[$i]:0;
                $name{$num-1} = $ch{1};
                $name{$num-2} = $ch{0};
                $r = ucfirst($name);
            }
            if($p == 3) {
                    for($i=0;$i<$amount;$i++)
                    ($lch==$f_end1[$i])?$ch=$f_end3[$i]:0;
                    (($lch=='ия')&&($num>3))?$ch='ии':0;
                    (($lch=='ия')&&($num<4))?$ch='ие':0;
                $name{$num-1} = $ch{1};
                $name{$num-2} = $ch{0};
                $r = ucfirst($name);
            }
            if($p == 4) {
                    for($i=0;$i<$amount;$i++)
                    ($lch==$f_end1[$i])?$ch=$f_end3[$i]:0;
                $name{$num-1} = $ch{1};
                $name{$num-2} = $ch{0};
                $r = ucfirst($name);
            }
            if ($p == 5) {
                    for($i=0;$i<$amount;$i++)
                    ($lch==$f_end1[$i])?$ch=$f_end3[$i]:0;
                $name{$num} = $ch{2};
                $name{$num-1} = $ch{1};
                $name{$num-2} = $ch{0};
                $r = ucfirst($name);
            }
            if ($p == 6) {
                    for($i=0;$i<$amount;$i++)
                    ($lch==$f_end1[$i])?$ch=$f_end3[$i]:0;
                    (($lch=='ия')&&($num>3))?$ch='ии':0;
                    (($lch=='ия')&&($num<4))?$ch='ие':0;
                $name{$num-1} = $ch{1};
                $name{$num-2} = $ch{0};
                $r = ucfirst($name); 
            }
            }
        }
        if ($s == 1) {
            $amount = count($m_end1);
            if(in_array($lch, $m_end1)) {
            if ($p == 2) {
                    for($i=0;$i<$amount;$i++)
                    ($lch==$m_end1[$i])?$ch=$m_end2[$i]:0;
                $name{$num} = $ch{2};
                $name{$num-1} = $ch{1};
                $name{$num-2} = $ch{0};
                $r = ucfirst($name);        
            }
            if ($p == 3) {
                    for($i=0;$i<$amount;$i++)
                    ($lch==$m_end1[$i])?$ch=$m_end3[$i]:0;
                $name{$num} = $ch{2};
                $name{$num-1} = $ch{1};
                $name{$num-2} = $ch{0};
                $r = ucfirst($name);
            }
            if ($p == 4) {
                    for($i=0;$i<$amount;$i++)
                    ($lch==$m_end1[$i])?$ch=$m_end4[$i]:0;
                $name{$num} = $ch{2};
                $name{$num-1} = $ch{1};
                $name{$num-2} = $ch{0};
                $r = ucfirst($name);
            }
            if ($p == 5) {
                    for($i=0;$i<$amount;$i++)
                    ($lch==$m_end1[$i])?$ch=$m_end5[$i]:0;
                $name{$num+1} = $ch{3};
                $name{$num} = $ch{2};
                $name{$num-1} = $ch{1};
                $name{$num-2} = $ch{0};
                $r = ucfirst($name);
            }
            if ($p == 6) {
                    for($i=0;$i<$amount;$i++)
                    ($lch==$m_end1[$i])?$ch=$m_end6[$i]:0;
                $name{$num} = $ch{2};
                $name{$num-1} = $ch{1};
                $name{$num-2} = $ch{0};
                $r = ucfirst($name);
            }
            }
        }
        return $r;
    }

    function prepareRules () {
        foreach ( array("lastName", "firstName", "middleName") as $type ) {
            foreach(array("suffixes" ,"exceptions") as $key) {
                $n = count($this -> rules->$type->$key);
                for ($i = 0; $i < $n; $i++) {
                    $this->rules->$type->{$key}[$i] = $this->rule($this->rules->$type->{$key}[$i]);
                }
            }
        }
    }

    function utf8_to_win($string){

        for ($c=0;$c<mb_strlen($string);$c++){
            $i=ord($string[$c]);
            if ($i <= 127) @$out .= $string[$c];
            if (@$byte2){
                $new_c2=($c1&3)*64+($i&63);
                $new_c1=($c1>>2)&5;
                $new_i=$new_c1*256+$new_c2;
                if ($new_i==1025){
                    $out_i=168;
                } 
                else {
                    if ($new_i==1105){
                        $out_i=184;
                    } 
                    else {
                        $out_i=$new_i-848;
                    }
                }
                @$out .= chr($out_i);
                $byte2 = false;
            }
            if (($i>>5)==6) {
                $c1 = $i;
                $byte2 = true;
            }
        }
    
        return $out;
    }

    function rule ($rule) {
        preg_match("/^\s*([fm]?)\s*(\S+)\s+(\S+)\s+(\S+)\s+(\S+)\s+(\S+)\s+(\S+)\s*$/u", $rule, $m);
            if ( $m ) return array (
            "sex" => $m[1],
            "test" => preg_split('/,/', $m[2]),
            "mods" => array ($m[3], $m[4], $m[5], $m[6], $m[7])
        );

        return false;
    }

    // склоняем слово по указанному набору правил и исключений
    function word ($word, $sex, $wordType, $gcase) {
        // исходное слово находится в именительном падеже
        if( $gcase == $this->gcaseNom) return $word;
        // составные слова
        if( preg_match("/[-]/", $word)) {
                $list = $word->split('-');
                $n = count($list);
                for($i = 0; $i < $n; $i++) {
                        $list[$i] = $this->word($list[$i], $sex, $wordType, $gcase);
                }
                return join('-', $list);
        }

        // Иванов И. И.
        if ( preg_match("/^[А-ЯЁ]\.?$/i", $word)) return $word;
        $this->init();
        $rules = $this->rules->$wordType;
        

        if ( $rules->exceptions) {
                $pick = $this->pick($word, $sex, $gcase, $rules->exceptions, true);
                if ( $pick ) return $pick;
        }
        
        $pick = $this->pick($word, $sex, $gcase, $rules->suffixes, false);
        if ($pick) return $pick;
        else return $word;
    }

    // выбираем из списка правил первое подходящее и применяем 
    function pick ($word, $sex, $gcase, $rules, $matchWholeWord) {
        $wordLower = strtolower($word);
        $n = count($rules);
        for($i = 0; $i < $n; $i++) {
            if ( $this->ruleMatch($wordLower, $sex, $rules[$i], $matchWholeWord)) {
                return $this->applyMod($word, $gcase, $rules[$i]);
            }
        }
        return false;
    }


    // проверяем, подходит ли правило к слову
    function ruleMatch ($word, $sex, $rule, $matchWholeWord) {
        if ($rule["sex"] == $this->sexM && $sex == $this->sexF) return false; // male by default
        if ($rule["sex"] == $this->sexF && $sex != $this->sexF) return false;
        $n = count($rule["test"]);
        for($i = 0; $i < $n; $i++) {
            $test = $matchWholeWord ? $word : mb_substr($word, max(mb_strlen($word) - mb_strlen($rule["test"][$i]), 0));
            if($test == $rule["test"][$i]) return true;
        }
        return false;
    }

    // склоняем слово (правим окончание)
    function applyMod($word, $gcase, $rule) {
        switch($gcase) {
            case $this -> gcaseNom: $mod = '.'; break;
            case $this -> gcaseGen: $mod = $rule["mods"][0]; break;
            case $this -> gcaseDat: $mod = $rule["mods"][1]; break;
            case $this -> gcaseAcc: $mod = $rule["mods"][2]; break;
            case $this -> gcaseIns: $mod = $rule["mods"][3]; break;
            case $this -> gcasePos: $mod = $rule["mods"][4]; break;
            default: exit("Unknown grammatic case: "+gcase);
        }
        $n = mb_strlen($mod);        
        for($i = 0; $i < $n; $i++) {
            $c = mb_substr($mod, $i, 1);            
            switch($c) {
                case '.': break;
                case '-': $word = mb_substr($word, 0, mb_strlen($word) - 1); break;
                default: $word .= $c;
            }
        }
        return $word;
    }
    
    function getSex() {
        if( mb_strlen($this->mn) > 2) {
            switch(mb_substr($this->mn, -2, 2)) {
                case 'ич': return $this->sexM;
                case 'на': return $this->sexF;
            }
        }
        return '';
    }
	
    function fullName($gcase) {

    	$tmpstr = ($this->fullNameSurnameLast ? '' : $this->lastName($gcase) . ' ')
            . $this -> firstName($gcase) . ' ' . $this -> middleName($gcase)
            . ($this -> fullNameSurnameLast ? ' ' . $this -> lastName($gcase) : ''); 

        return preg_replace("/^ +| +$/", '', $tmpstr);
    }
    
    function lastName($gcase) {
        return $this->word($this -> ln, $this -> sex, 'lastName', $gcase);  
    }
    
    function firstName($gcase) {
        return $this->word($this -> fn, $this -> sex, 'firstName', $gcase);
    }
    
    function middleName($gcase) {
        return $this->word($this -> mn, $this -> sex, 'middleName', $gcase);
    }
}
?>