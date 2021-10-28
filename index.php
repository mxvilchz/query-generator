<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Query Generator</title>
</head>
<body>
  <form method="POST" enctype="multipart/form-data">
    <input type="file" name="file">
    <input type="text" name="idGroupLast" placeholder="Id del último grupo">
    <input type="submit" value="Importar" />
  </form>
  <?php
    require_once 'SimpleXLSX.php';
    if (isset($_FILES['file'])) {
      if ( $xlsx = SimpleXLSX::parse( $_FILES['file']['tmp_name'] ) ) {
        echo '<h1>Queries</h1>';

        // FORMULARIOS

        $dim = $xlsx->dimension(0);
        $cols = $dim[0];

        echo '<h2>'.$xlsx->sheetName(0).'</h2>';
        
        for ( $i = 0; $i < $cols; $i ++ ) {
          $cont = 0;

          if ($i > 3) {
            break;
          }

          if ($i !== 1) {
            echo '<pre>';
            echo '<code>';
  
            if ($i === 3) {
              echo 'insert into formularios.formulario_componente (f, v, l, rq_w, ro_w, hd_w, rq_m, ro_m, hd_m, t, st, vl, ai, s, sm, sv, so) values' . '</br>';
            }
  
            foreach ( $xlsx->rows(0) as $k => $r ) {
              if ($k == 0) continue;
              
              if ($i === 0) {
                if (!empty($r[$i])) {
                  echo 'insert into formularios.forms (ai) values ({"t": "'.$r[$i].'", "v": ["1"], "max": '.$r[1].', "min": 1});' . '</br>';
                }
              }
  
              if ($i === 2) {
                if (!empty($r[$i])) {
                  $cont++;
                  echo 'insert into formularios.form_section (section_data, form_id, section_active, section_order) values ({"t": "'.$r[$i].'"}, form_id, 1, '.$cont.');' . '</br>';
                }
              }
              
              if ($i === 3) {
                if (!empty($r[$i])) {
                  $cont++;
  
                  $obj = '{"d": "", "t": "'.$r[$i].'", "v": "'.$r[12].'"}';
                  echo "(form_id, ".$cont.", '".$r[$i]."', ".$r[6].", ".$r[7].", ".$r[8].", ".$r[9].", ".$r[10].", ".$r[11].", '".$r[4]."', '".$r[5]."', '[]', null, section_id, '".$obj."', null, ".$cont.")," . '</br>';
                }
              }
            }
  
            echo '</code>';
            echo '</pre>';
            echo '</br>';
          } 
        }

        // LISTAS DE OPCIONES

        $dim = $xlsx->dimension(1);
        $cols = $dim[0];

        $idGroupLast = isset($_POST['idGroupLast']) ? intval($_POST['idGroupLast']) : 4444;
        
        echo '<h2>'.$xlsx->sheetName(1).'</h2>';

        for ( $i = 0; $i < $cols; $i ++ ) {
          $id = 0;
          $idGroupLast++;
          
          echo '<pre>';
          echo 'insert into formularios.formulario_combo_data (group_id, data_value, data_label, additional_info, parent_combo_data_id, parent_group_id, data_order) values' . '</br>';
            foreach ( $xlsx->rows(1) as $k => $r ) {
              if ($k == 0) continue; // skip first row
              if (!empty($r[$i])) {
                $id++;
                echo "(".$idGroupLast.", '".$id."', '".$r[$i]."', '{}', null, null, ".$id.")," . '</br>';
              }
            }
          echo '</pre>';
          echo '</br>';

        }

      } else {
        echo SimpleXLSX::parseError();
      }
    }
  ?>
</body>
</html>