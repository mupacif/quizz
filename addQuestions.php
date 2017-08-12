<!DOCTYPE html>
<html>
  <head>
    <title>Questions </title>
    </head>
    <body>
  <!--   redirect if there is no id -->
    <?php 
if(!isset($_GET["id"] ) || empty($_GET["id"]))
{
   $host  = $_SERVER['HTTP_HOST'];
$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
$extra = 'index.html';
echo "<script>window.location.replace('http://$host$uri/$extra')</script>";
}
else
{
  $id = htmlspecialchars($_GET["id"]);
}

?>

<div id="vue-instance">

 <a href="index.html">back to home</a> 
 <h3> Add question </h3>
  <textarea type="text" v-model="questionTextArea" placeholder="question"  rows="5" cols="100"> </textarea> <br>
  <button v-on:click="addQuestion" :disabled="!disabledAnswer && !updatable" >
  <span v-if="disabledAnswer">Add</span>
  <span v-if="!disabledAnswer">update</span>
  </button> 


   <button v-on:click="addNextQuestion" " :disabled="disabledAnswer">next Question</button>


 <!--   Display questions -->
  <ul><li v-for="answer in answers"> 

  <textarea type="text" placeholder="add some text" v-model="answers[$index].name"  rows="3" cols="90" :disabled="disabledAnswer" @change="updateCorrectAnswer($index)"> </textarea>

<input type="checkbox" id="{{answer.id}}" v-model="answers[$index].correct" @click="updateCorrectAnswer($index)">
   |
   <a href="#" @click="deleteAnswer(answer.id,$index)">delete</a>
   </li>


<!--    Add new question -->
<br/><br/>

    <textarea type="text" v-model="answerTextArea" placeholder=" add new answer"  rows="3" cols="90" :disabled="disabledAnswer"> </textarea> 
    <input type="checkbox" name="correctAnswer" v-model="correctCb" :disabled="disabledAnswer"><br>
  <button v-on:click="addAnswer"  :disabled="disabledAnswer" >Add answer</button>
  </ul>

<!--   display all questions -->
<ul><li v-for="q in questions">
<a href="#" @click="loadQuestion(q.id,$index)">
{{q.name}} 
</a>

<!-- delete -->
<a href="#" @click="deleteQuestion(q.id)">delete</a>

</li></ul>

</div>
  <script src="//cdn.jsdelivr.net/vue/1.0.16/vue.js"></script>
  <script src="https://unpkg.com/lodash@4.13.1/lodash.min.js"></script>
  <script src="//unpkg.com/axios/dist/axios.min.js"></script>

  <script>
    // our VueJs instance bound to the div with an id of vue-instance


    var vm = new Vue({
    		el: "#vue-instance",
    		data:{
    			idChapter: <?php echo $id ?>,
    			questions:[],
    			questionTextArea:"",
    			currentQuestion: 0,
    			answers:[],
    			answerTextArea:"",
    			correctCb:false,
    			disabledAnswer:true,
          updatable:false,
          checkedQuestions:[]

    		},
    		created:function()
    		{
            axios.get('web/chapter/'+this.idChapter)
            .then(function (response) {
            	vm.questions = response.data;

            }).catch(function(error)
            {
            			console.log("cant load anything")
            })
            ;
    		},
        watch:{
          /** will be called if a change occurs **/
          questionTextArea:function()
          {
              this.updatable = true;
          }
        },
    		methods:
    		{
    			addQuestion:function()
    			{
//1st time : save
      if(this.disabledAnswer)
      {
        console.log("add questions")
    				questionData = {id:-1,name: this.questionTextArea, idChapter : this.idChapter};
    				axios.post('web/addQuestion', questionData)
          .then(function (response) {
            questionData.id = response.data.id;
            vm.currentQuestion = response.data.id;
        	vm.questions.push(questionData);
        	vm.disabledAnswer=false
       	
          })
          .catch(function (error) {
            console.log(error);
          }); 
        }else
        {
          console.log("update questions")
          //2nd time = update
                  axios.put('web/setQuestion', {idQuestion:this.currentQuestion,question:this.questionTextArea})
          .then(function (response) {

               vm.refresh();
        
          })
          .catch(function (error) {
            console.log(error);
          }); 
        }

   
      this.updatable = false;
    			},
    			addAnswer:function()
    			{
    				answer = {idQuestion: this.currentQuestion, name:this.answerTextArea,correct:this.correctCb};


    				axios.post('web/addAnswer', answer)
          .then(function (response) {
	            vm.answers.push(answer);

    				vm.answerTextArea = "";
    				vm.correctCb = false;
       	
          })
          .catch(function (error) {
            console.log(error);
          }); 

    			},
          deleteAnswer:function(id,index)
          {
             axios.delete('web/answer/'+id)
                    .then(function (response) {
                        console.log("answer deleted")
                vm.answers.splice(index,1)
          })
          .catch(function (error) {
            console.log(error);
          }); 
          },
          updateCorrectAnswer:_.debounce(function(index)
          {
            console.log("update answer:"+index);
            let idAnswer = this.answers[index].id;
            let answer = this.answers[index].name;
            let correct = this.answers[index].correct;
            let link = 'web/answer/'+idAnswer;
            console.log(link);
          axios.put(link ,{answer:answer,correct:correct}).
          then(function(response)
          {
            console.log(response.data);
          })
          },500
          )
          ,addNextQuestion:function()
    			{
    				
    				this.questionTextArea = ""
    					this.answerTextArea = ""
    				this.correctCb = false
    				this.currentQuestion = -1
    				this.disabledAnswer=true
            this.updatable = false
            this.answers=[]
    			},
          deleteQuestion:function(id)
          {
            
            axios.delete('web/question/'+id)
                    .then(function (response) {
                        console.log("question deleted")
            vm.refresh()
          })
          .catch(function (error) {
            console.log(error);
          }); 
  },
  /***
  *load questions + its answer when user click on it
  *into the list
  */
  loadQuestion:function(id,index)
  {
      console.log("load question:"+id);
 axios.get('web/answers/'+id)
            .then(function (response) {
               vm.answers = response.data;
               vm.currentQuestion = id;
                vm.questionTextArea = vm.questions[index].name;
                    vm.answerTextArea = "";
            vm.correctCb = false;
            vm.disabledAnswer=false

            }).catch(function(error)
            {
              console.log(error);
            }
            );
                 
 
          
  },
    refresh:function()
        { 
           
          axios.get('web/chapter/'+this.idChapter)
            .then(function (response) {
              vm.questions = response.data;
            });
      }
    		}
    	});
    	</script>
</body>
</html>