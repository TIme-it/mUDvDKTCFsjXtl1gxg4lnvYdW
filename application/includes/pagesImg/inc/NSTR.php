<?PHP
  $LMenu = '';
  $result_ = mysql_query('SELECT Code,Title,T_Page,T_Code '.
                         'FROM '.$NODE->Table.' '.
                         'WHERE (Code='.$NODE->line[7].')and(del!="Y")and(visibled="Y")', $link);
  if($lin = mysql_fetch_row($result_)){
    switch($lin[2]){
    /*case 'REPR':
    case 'ART' :
    case 'NEWS':
    case 'TPAG':*/
        /*$result_ = @mysql_query('SELECT Code,Title '.
                                'FROM '.$NODE->Table.' '.
                                'WHERE (T_Code='.$lin[3].')and(del!="Y") '.
                                'ORDER BY Tree desc', $link);
        if(mysql_num_rows($result_)>0){
          while($line_ = mysql_fetch_row($result_))
            if($line_[0] == $NODE->line[0])
              $topMenu[] = '<strong>'.$line_[1].'</strong>';
            else
              $topMenu[] = '<a href="?id='.$line_[0].'&'.SELMENU.'" class="Red">'.$line_[1].'</a> ';
        }*/
    case 'NPAG':
        if($NODE->isLMenu){
          if($lin[2] == 'NPAG'){
            if($lin[3]!='0')
              if($lin_ = mysql_fetch_row(mysql_query('SELECT Code,Title,T_Page,T_Code '.
                                                     'FROM '.$NODE->Table.' '.
                                                     'WHERE (Code='.$lin[3].')and(del!="Y")and(visibled="Y")'.$Site->krn, $link))){
                if($lin_[2] == 'REPR' ||
                   $lin_[2] == 'ART' ||
                   $lin_[2] == 'NEWS' ||
                   $lin_[2] == 'TPAG')
                  $Site->PageTitle.=' '.TITLE_DELIM.' '.$lin_[1];

                $result_ = @mysql_query('SELECT Code,Title '.
                                        'FROM '.$NODE->Table.' '.
                                        'WHERE (T_Code='.$lin_[0].')and(del!="Y")and(visibled="Y")'.$Site->krn, $link);
                while($lin_ = mysql_fetch_row($result_)){
                  $NODE->LMenu[] = array(NULL, $lin_[0], $lin_[1]);
                  if($lin_[0] == $lin[0]){
                    $result2_ = @mysql_query('SELECT Code,Title,DATE_FORMAT(Date_,"'.D_FORMAT.'") '.
                                             'FROM '.$NODE->Table.' '.
                                             'WHERE (T_Code='.$lin_[0].')and(del!="Y")and(visibled="Y")'.$Site->krn.' '.
                                             'ORDER BY Date_ desc,Tree '.ORDER_TREE.' '.
                                             'LIMIT 0,11', $link);
                    if(mysql_num_rows($result2_)>0){
                      $i=1;
                      while($lin2_ = mysql_fetch_row($result2_)){
                        $NODE->LMenu[count($NODE->LMenu)-1]["node"][] = array($lin2_[0], $lin2_[1], $lin2_[2]);
                        $i++;
                        if($i>10) break;
                      }
                      if(mysql_num_rows($result2_)>10)
                        $NODE->LMenu[count($NODE->LMenu)-1]["arc"] = true;
                    }
                  }
                }
            }
            else{
              $result2_ = @mysql_query('SELECT Code,Title,DATE_FORMAT(Date_,"'.D_FORMAT.'") '.
                                       'FROM '.$NODE->Table.' '.
                                       'WHERE (T_Code='.$lin[0].')and(del!="Y")and(visibled="Y")'.$Site->krn.' '.
                                       'ORDER BY Date_ desc,Tree '.ORDER_TREE.' '.
                                       'LIMIT 0,11', $link);
              if(mysql_num_rows($result2_)>0){
                $i=1;
                $NODE->LMenu[0][1] = $lin[0];
                $NODE->LMenu[0][2] = $lin[1];
                while($lin2_ = mysql_fetch_row($result2_)){
                  $NODE->LMenu[0]["node"][] = array($lin2_[0], $lin2_[1], $lin2_[2]);
                  $i++;
                  if($i>10) break;
                }
                if(mysql_num_rows($result2_)>10)
                  $NODE->LMenu[0]["arc"] = true;
              }
            }
          }
        }
        elseif($lin[3]!='0' && $lin[2] == 'NPAG'){
          $result_ = mysql_query('SELECT Code,Title,T_Page,T_Code '.
                                 'FROM '.$NODE->Table.' '.
                                 'WHERE (Code='.$lin[3].')and(del!="Y")and(visibled="Y")', $link);
          if($lin_ = mysql_fetch_row($result_))
            if($lin_[2] == 'REPR' ||
               $lin_[2] == 'ART' ||
               $lin_[2] == 'NEWS' ||
               $lin_[2] == 'TPAG')
              $Site->PageTitle.=' '.TITLE_DELIM.' '.$lin_[1];
        }

        $Site->PageTitle.= ' '.TITLE_DELIM.' '.$lin[1];
    break;
    }
  }
?>