<?php
/****************************************************

          simplemarkup, version 0.4
          (now simplyfied compared to v.0.3
          as lists are not suported any more
          because lists don'T validate in
          <span>-type popups)

          for CMSimple calendar plugin
  Transforms multiline simple text marked up with
  "*","_","~" to HTML without <p></p>
  for use in table fields and pop-ups and vici versa

  2/2012 by svasti www.svasti.de --
****************************************************/


$formatting_hints = '<table><tr>'

                  . '<td>abc\-def</td>'
                  . '<td>&rarr;' . tag('br')
                  . '&rarr; </td>'
                  . '<td>abcdef' . tag('br')
                  . '&nbsp; &nbsp; &nbsp; abc-' . tag('br')
                  . 'def</td>'
                  . "</tr><tr>\n"

                  . '<td>&nbsp;</td></tr><tr>'

                  . '<td>==abc== </td>'
                  . '<td>&rarr; &nbsp; &nbsp; </td>'
                  . '<td><big>abc</big></td>'
                  . "</tr><tr>\n"

                  . '<td>**abc**</td>'
                  . '<td>&rarr;</td>'
                  . '<td><b>abc</b></td>'
                  . "</tr><tr>\n"

                  . '<td>*abc*</td>'
                  . '<td>&rarr;</td>'
                  . '<td><i>abc</i></td>'
                  . "</tr><tr>\n"

                  . '<td>++abc++</td>'
                  . '<td>&rarr;</td>'
                  . '<td><span style="color:red;">abc</span></td>'
                  . "</tr><tr>\n"

                  . '<td>_abc_</td>'
                  . '<td>&rarr;</td>'
                  . '<td><u>abc</u></td>'
                  . "</tr><tr>\n"

                  . '<td>__abc__</td>'
                  . '<td>&rarr;</td>'
                  . '<td><small>abc</small></td>'
                  . "</tr><tr>\n"

                  . '<td>\\*</td>'
                  . '<td>&rarr;</td>'
                  . '<td>*</td>'
                  . '</tr></table>';



//converts text (formatted with *,_,#.~ and \n) to html for table data cells without paragraphs
function simpleMarkupToHtml($text)
{

    // soft hyphens
    $text = str_replace('\-','­',$text);

    $pattern = array(
        '/==([^=]+)==/',               //1 - ==big==
        '/\*\*([^\*]+)\*\*/',          //2 - **bold**
        '/(?<!\\\)\*([^\*]+)\*/',      //3 - *italic*, exept escaped *
       '/\+\+([^\+]+)\+\+/',           //4 - ++red++
        '/\b_([^_]+)_/',               //5 - _underlined_, word like a_b are excluded because of "\b"
        '/__([^_]+)__/',               //6 - __small__
        '/(\\\)\*/',                   //7 - escaped *

        '/\s*\n*\s*$/',                //12- delete all empty lines at the end
        '/\n/e'                        //13- transform any remaining line breaks to <br>
    );

    $replacement = array(
        '<span class="big">\1</span>',               //1 - <big>big</big>
        '<b>\1</b>',                   //2 - <b>bold</b>
        '<i>\1</i>',                   //3 - <i>italic</i>
        '<span class="red">$1</span>', //4 - <span class="red">red</span>
        '<u>\1</u>',                   //5 - <u>underlined</u>
        '<small>\1</small>',           //6 - <small>small</small>
        '*',                           //7 - *

        '',                            //12- no empty lines at the end
        "tag('br')"                    //13- replace line breaks by <br>
    );

    $text = preg_replace($pattern, $replacement, $text);

    $text = str_replace(array(chr(10),chr(13),'\\\'','\\"','\\*'),array('','','\'','"','*'), $text);

return $text;
}

//converts simple html without linebreaks+paragraphs  to plain text formatted with *,_,# and linebreaks
function htmlToSimpleMarkup($text)
{
    $text = str_replace(
        array(
            '</p><p>',
            'strong>',
            'em>',
            '­',        //soft hyphen
            '<big>',
            '</big>'
        ),
        array(
            '<br>',
            'b>',
            'i>',
            '\-',
            '<span class="big">',
            '</span>'
        ),
        $text
    );

    $text = preg_replace(
        array(
            '/\*/',                                         //1 - *
            '/<span class=\"big\">([\S+|\S+\s]+)<\/span>/U',                //2 - <big>big</big>
            '/<b>([\S+|\S+\s]+)<\/b>/U',                    //3 - <b>bold</b>
            '/<i>([^_]+)<\/i>/U',                           //4 - <i>italic</i>
            '/<span class=\"red\">([\S+|\S+\s]+)<\/span>/U',  //5 - <span class="red">red</span>
            '/<u>([\S+|\S+\s]+)<\/u>/U',                    //6 - <u>underlined</u>
            '/<small>([^_]+)<\/small>/U',                   //7 - <small>small</small>
            '/<br( \/)?>/'                                  //8 - line break
        ),
        array(
            '\\*',             //1 - escaped *
            '==\1==',          //2 - ==big==
            '**\1**',          //3 - **bold**
            '*\1*',            //4 - *italic*
            '++\1++',          //5 - ++red++
            '_\1_',            //6 - _underlined_
            '__\1__',          //7 - __small__
            chr(10),           //8 - new line
        ),
        $text
    );

return $text;
}
