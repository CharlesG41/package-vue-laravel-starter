<?php

namespace Cyvian\Src\app\Handlers\Utils;

use Cyvian\Src\app\Classes\Form;

class MergeForm
{
    public function handle(Form $form1, Form $form2)
    {
//        return $form1;
        $sections = array_merge($form1->sections, $form2->sections);
        $form1->sections = $sections;
        return $form1;
        dd(array_merge($form1->sections, $form2->sections));
        $sections = [];
        // loop through sections of form1
        // loop through sections of form2
        // if section.id of form1 is equal to section.id of form2
            // loop through fields of section of form1
            // loop through fields of section of form2
            // if field.id of form1 is equal to field.id of form2
                // take field of form2 and put it in section of form1
                // if field has children fields -- recursive
                    // loop through children fields of field 1
                        // loop through children fields of field 2
                            // if child field.id of form1 is equal to child field.id of form2
                                // replace child field of form1 for child field of form 2
                            // else
                                // put child field of form2 in children fields of form1
            // else
                // push field of form2 in section of form1
            //put section of form 1 in sections array
        // else
            // put section of form2 in sections array


        // loop through sections of form1
//        foreach ($form1->sections as $section1) {
//            // loop through sections of form2
//            foreach($form2->sections as $section2) {
//                // if section.id of form1 is equal to section.id of form2
//                if ($section1->id == $section2->id) {
//                    // loop through fields of section of form1
//                        foreach($section1->fields as $field1) {
//                            foreach($section2->fields as $field2) {
//                                // if field.id of form1 is equal to field.id of form2
//                                if ($field1->id == $field2->id) {
//                                    // take field of form2 and put it in section of form1
//                                    // if field has children fields -- recursive
//                                    if (has_property($field1, 'fields')) {
//                                        $fields = $this->mergeFields($field1, $field2);
//                                    }
//                                    // loop through children fields of field 1
//                                    // loop through children fields of field 2
//                                    // if child field.id of form1 is equal to child field.id of form2
//                                    // replace child field of form1 for child field of form 2
//                                } else {
//                                    // put child field of form2 in children fields of form1
//                                    $section1->fields[] = $field2;
//                                }
//                            }
//                        }
//                } else {
//
//                }
//
//                // if field.id of form1 is equal to field.id of form2
//                // take field of form2 and put it in section of form1
//                // if field has children fields -- recursive
//                // loop through children fields of field 1
//                // loop through children fields of field 2
//                // if child field.id of form1 is equal to child field.id of form2
//                // replace child field of form1 for child field of form 2
//                // else
//                // put child field of form2 in children fields of form1
//                // else
//                // put field of form2 in section of form1
//                //put section of form 1 in sections array
//                // else
//                // put section of form2 in sections array
//            }
//        }


        $form1->sections = array_merge($form1->sections, $form2->sections);

        foreach($form1->sections as $sectionForm1) {
            foreach($form2->sections as $sectionForm2) {
                if($sectionForm1->id == $sectionForm2->id) {
                    $sectionForm1->fields = array_merge($sectionForm1->fields, $sectionForm2->fields);
                    foreach($sectionForm1 as $fieldForm1) {
                        foreach($sectionForm2 as $fieldForm2) {
                            if($fieldForm1->id == $fieldForm2->id) {
                                $fieldForm1->attributes = array_merge($fieldForm1->attributes, $fieldForm2->attributes);
                            } else {
                                $sectionForm1->fields[] = $fieldForm2;
                            }
                        }
                    }
                } else {
                    $form1->sections[] = $sectionForm2;
                }
            }
        }

        return $form1;
    }
}
