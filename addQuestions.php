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
  <button v-on:click="addQuestion" :disabled="!disabledAnswer" >Add</button>  <button v-on:click="addNextQuestion" " :disabled="disabledAnswer">next Question</button>
  <ul><li v-for="answer in answers"> {{answer.name}}  <span v-if="answer.correct"> ok </span>
   </li>

    <textarea type="text" v-model="answerTextArea" placeholder="answer"  rows="3" cols="90" :disabled="disabledAnswer"> </textarea> 
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
    			disabledAnswer:true

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

    		methods:
    		{
    			addQuestion:function()
    			{

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

    			},addNextQuestion:function()
    			{
    				
    				this.questionTextArea = ""
    					this.answerTextArea = ""
    				this.correctCb = false
    				this.currentQuestion = -1
    				this.disabledAnswer=true
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