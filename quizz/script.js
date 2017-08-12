

var totalCorrects = 0;

var submit =document.getElementById("submit");
submit.addEventListener("click",checkCorrectAnswers,false);

function checkCorrectAnswers()
{

    totalCorrects = 0;
    console.log("checkCorrectAnswers")
 
   var allInputs = document.querySelectorAll('input[type=radio]')

for(var i=0;i<allInputs.length;i++)
{
   
 var question=allInputs[i];

 if(question.value==1)
   {
       if(question.checked)
            totalCorrects++
       question.className += "coloredGreen"
   }

 if(question.value!=1 && question.checked)
   {
       question.className += "coloredRed"
   }
   
 
}
alert("Вы ответили правильно на "+totalCorrects+"/5");
}



//Here is the reset button*/
var reset =document.getElementById("reset");
reset.addEventListener("click",resetClicked,false);
function resetClicked()
{
 //reset the counter
  totalCorrects = 0;

   var allInputs = document.querySelectorAll('input[type=radio]')

//we count all inputs
for(var i=0;i<allInputs.length;i++)
{
    //we take one by one
 var question=allInputs[i];

       question.className = ""
       question.checked = false;
 
}
}
