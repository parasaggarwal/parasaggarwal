<?php

/**
 * LICENSE: This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 3
 * of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see
 * http://www.gnu.org/licenses/licenses.html#GPL .
 *
 * @package OpenEMR
 * @license http://www.gnu.org/licenses/licenses.html#GPL GNU GPL V3+
 * @author  Dror Golan <drorgo@matrix.co.il>
 * @link    http://www.open-emr.org
 */


/**get all the validation on the page
 * @param $title
 * @return array of validation rules and forms names
 */
function collectValidationPageRules($title,$active=true){

    if($active){
        $sql = sqlStatement("SELECT * " .
            "FROM `list_options` WHERE list_id=? AND activity=?  AND title=?",array('page_validation',1,$title));
    }
    else {
        $sql = sqlStatement("SELECT * " .
            "FROM `list_options` WHERE list_id=? AND title=?", array('page_validation', $title));
    }

    $dataArray=array();
    while($row = sqlFetchArray($sql) ) {
        $formPageNameArray = explode('#', $row['option_id']);
        $dataArray[$formPageNameArray[1]]=array('page_name' => $formPageNameArray[0] ,'rules' => $row['notes']);
    }
    return $dataArray;

}

/**this function creates client side validation rules for each <form> declared in list : Patient Validation - patient_validation
 * @param $fileNamePath
 * @output a generated javascript tag with the validation
 */
function validateUsingPageRules($fileNamePath)
{

$path='';

if($GLOBALS['webroot']!='')
{
    $path= str_replace($GLOBALS['webroot'], '',$fileNamePath);
}
else{
    $path=$fileNamePath;
}

print '<!--Page Form Validations-->';
//if we would like to get all the page forms rules we need to call collectValidationPageRules($title) this way there is a
$collectThis=collectValidationPageRules($path);
if ($collectThis) {



        print '<!---Start of page  form validation-->';
        print '<!--//include new rules of submitme functionallity-->';
        echo("\r\n");
        require_once($GLOBALS['srcdir'] . "/validation/validation_script.js.php");
        $new_validate = $GLOBALS['new_validate'] ? 1 : 0;
        echo("\r\n");
        print '<script type="text/javascript">';

        foreach ($collectThis as $key => $value) {
            echo("try{");
            echo("\r\n");
            echo('if(document.getElementsByName("' . $key . '").length>0)');
            echo("\r\n");
            echo('{');
            echo("\r\n");
            echo('var form = document.getElementsByName("<?php echo $key;?>");');
            echo("\r\n");
            echo('form.id = "<?php echo $key;?>";');
            echo("\r\n");
            echo('<!--Use validation script js Validations-->');
            echo("\r\n");
            echo('$("form").attr("onclick", \'return submitme(' . $new_validate . ',event,"' . $key . '",' . $collectThis[$key]['rules'] . ')\');');
            echo("\r\n");
            echo('}');
            echo("\r\n");
            echo('}');
            echo("\r\n");
            echo('catch(err)');
            echo("\r\n");
            echo('{');
            echo("\r\n");
            echo('//log err - console.log(err)');
            echo("\r\n");
            echo('}');
        }
        echo('</script>');
    print '<!---End of page  form validation-->';
    }
}

