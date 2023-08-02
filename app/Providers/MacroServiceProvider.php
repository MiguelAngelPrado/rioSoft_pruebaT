<?php
namespace App\Providers;
use Form;
use Illuminate\Support\ServiceProvider;

class MacroServiceProvider extends ServiceProvider{

	public function boot(){
		Form::macro('mtext', function($name,$value=null,$placeholder,$errors,$col,$required=false,$label= true,$html=null){
			$input ='';
            $class ='';
            $input .= '<div class="col-'.$col.'">';
            $input .= '<div class="form-group">';
            if($value == null){
                $value = old($name);
            }
            if($required){
                
                if($errors->has($name)){
                    $class =  "form-control is-invalid";
                    $errors = '<span class="invalid-feedback" role="alert">'.
                                "<strong>".$errors->first($name)."</strong>".
                                '</span>';
                }else{
                    $errors='';
                    $class =  'form-control';
                }
               
            }
            else{
                
                $errors='';
                $class =  'form-control';
                
            }
            $style = ['class'=>$class,'placeholder'=>$placeholder,'id'=>$name,'autocomplete'=>'off'];
            if($html != null){
                for($i=0;$i<count($html);$i++){
                   if(key($html[$i]) != '0'){
                    $style[key($html[$i])] = $html[$i][key($html[$i])];
                   }else{
                    $style[$html[$i][key($html[$i])]] = $html[$i][key($html[$i])];
                   }
                }
            }
            if($label)
                $input.= '<label class="form-control-label text-color-dark" for="'.$name.'">'.$placeholder.'</label>';
            
            $input.= Form::text($name,$value,$style);
            $input.=$errors;
            $input.= '</div></div>';
            return $input;
		});

		Form::macro('mtextAr', function($name,$value=null,$placeholder,$errors,$col,$required=false,$disabled=false){
			$input ='';
            $class ='';
            $input .= '<div class="col-sm-'.$col.'">';
            $input .= '<div class="form-group">';
            if($value == null){
                $value = old($name);
            }
            if($required){
                
                if($errors->has($name)){
                    $class =  "form-control is-invalid";
                    $errors = '<span class="invalid-feedback" role="alert">'.
                                "<strong>".$errors->first($name)."</strong>".
                                '</span>';
                }else{
                    $errors='';
                    $class =  'form-control';
                }
               
            }
            else{
                
                $errors='';
                $class =  'form-control';
                
            }
            if($disabled)
                $disabled = 'disabled';
            else
                $disabled = '';
            $input.= '<label class="form-label text-color-dark" for="'.$name.'">'.$placeholder.'</label>';
            $input.= '<textarea '.$disabled.' name="'.$name.'" class="'.$class.'" placeholder="'.$placeholder.'" id="'.$name.'">'.$value.'</textarea>';//Form::text($name,$value,$style);
            $input.=$errors;
            $input.= '</div></div>';
            return $input;
		});

		Form::macro('mselect', function($name,$options=[],$value=null,$placeholder,$errors,$col,$required=false,$otro = false,$actions = null)
        {
            $input ='';
            $class ='';
            $input .= '<div class="col-sm-'.$col.'">';
            $input .= '<div class="form-group">';
            if($otro){
                array_push($options,'otro');
            }
            if($value == null){
                $value = old($name);
            }
            if($required){
                
                if($errors->has($name)){
                    $class =  "form-control form-control text-3 select2 is-invalid";
                    $errors = '<span class="invalid-feedback" role="alert">'.
                                "<strong>".$errors->first($name)."</strong>".
                                '</span>';
                }else{
                    $errors='';
                    $class =  'form-control form-control text-3 select2';
                }
               
            }
            else{
                
                $errors='';
                $class =  'form-control form-control text-3 select2';
                
            }
            if($actions != null){
                $actions = 'onchange="'.$actions.'"';
            }else{
                $actions = '';
            }

            $input.= '<label  class="form-label text-color-dark text-3" for="'.$name.'">'.$placeholder.'</label>';
            $input.= Form::select($name,$options,$value,['class'=>$class,'style'=>'width: 100%;','id'=>$name,'placeholder'=>'Seleccionar',$actions]);
            $input.=$errors;
            $input.= '</div></div>';
            return $input;
        });

        Form::macro('mselectp', function($name,$options=[],$value=null,$placeholder,$errors,$col,$required=false,$otro = false,$actions = null)
        {
            $input ='';
            $class ='';
            $input .= '<div class="col-sm-'.$col.'">';
            $input .= '<div class="form-group">';
            if($otro){
                array_push($options,'otro');
            }
            if($value == null){
                $value = old($name);
            }
            if($required){
                
                if($errors->has($name)){
                    $class =  "form-control form-control-lg text-3 select2bs4 is-invalid";
                    $errors = '<span class="invalid-feedback" role="alert">'.
                                "<strong>".$errors->first($name)."</strong>".
                                '</span>';
                }else{
                    $errors='';
                    $class =  'form-control form-control-lg text-3 select2bs4';
                }
               
            }
            else{
                
                $errors='';
                $class =  'form-control form-control-lg text-3 select2bs4';
                
            }
            if($actions != null){
                $actions = 'onclick="'.$actions.'"';
            }else{
                $actions = '';
            }

            $input.= '<label  class="form-label text-color-dark text-3" for="'.$name.'">'.$placeholder.'</label>';
            $input.= Form::select($name,$options,$value,['class'=>$class,'style'=>'width: 100%;','id'=>$name,'placeholder'=>'Seleccionar',$actions]);
            $input.=$errors;
            $input.= '</div></div>';
            return $input;
        });
	}
}