<?php

//common environment attributes including search paths. not specific to Learnosity
include_once '../../env_config.php';

//site scaffolding
include_once 'includes/header.php';

//common Learnosity config elements including API version control vars
include_once '../../lrn_config.php';

use LearnositySdk\Request\Init;
use LearnositySdk\Utils\Uuid;

$session_id = Uuid::generate();

$security = [
    'user_id'      => 'demos-site',
    'domain'       => $domain,
    'consumer_key' => $consumer_key
];

$request = '{
  "id": "custom-percentage-bar",
  "name": "Custom Percentage Bar",
  "type": "local_practice",
  "state": "initial",
  "session_id": "' . $session_id . '",
  "questions": [
    {
      "response_id": "custom-percentage-bar-response-1",
      "type": "custom",
      "js": {
        "question":"//' . $_SERVER['HTTP_HOST'] . '/usecases/customquestions/custom_percentage_bar_q.js",
        "scorer":"//' . $_SERVER['HTTP_HOST'] . '/usecases/customquestions/custom_percentage_bar_s.js"
      },
      "css": "//' . $_SERVER['HTTP_HOST'] . '/usecases/customquestions/custom_percentage_bar.css",
      "stimulus": "If Luke has $150 and he spends $30 on beer, how much money has he got left?",
      "prepend_unit": "$",
      "append_unit": "",
      "min_value": "0",
      "max_value": "150",
      "step": "10",
      "min_percentage": 0,
      "max_percentage": 100,
      "init_value": "20",
      "bar_color": "#9ae5c9",
      "valid_response": "120",
      "score": 1
    }
  ]
}';


$init = new Init('questions', $security, $consumer_secret, $request);
$signedRequest = $init->generate();

?>

<style>
    .custom-score {
        position: absolute;
        font-size: 17px;
        margin-top: 5px;
    }
</style>

<div class="jumbotron section">
     <div class="toolbar">
        <ul class="list-inline">
            <li data-toggle="tooltip" data-original-title="Preview API Initialisation Object"><a href="#"  data-toggle="modal" data-target="#initialisation-preview" aria-label="Preview API Initialisation Object"><span class="glyphicon glyphicon-search"></span></a></li>
            <li data-toggle="tooltip" data-original-title="Visit the documentation"><a href="https://support.learnosity.com/hc/en-us/articles/360000758817-Creating-Custom-Questions" title="Documentation"><span class="glyphicon glyphicon-book"></span></a></li>

        </ul>
    </div>
    <div class="overview">
        <h1>Custom Question - Percentage Bar</h1>
        <p>Demostrates the implementation of a Custom question with an interactive and more complex UI.</p>
    </div>
</div>

<div class="section">
    <div class="row">
        <div class="question-container">
            <span class="learnosity-response question-custom-percentage-bar-response-1"></span>
            <div class="custom-score"><strong>Score: </strong> <span id="question_score">0</span> / <span id="question_max_score">0</span></div>
            <button class="btn btn-primary pull-right" id="validate_question">Check Answer</button>
        </div>
    </div>
</div>

<script src="<?php echo $url_questions; ?>"></script>
<script>

    var questionsApp = window.questionsApp = LearnosityApp.init(<?php echo $signedRequest; ?>,  {
        errorListener: window.widgetApiErrorListener,
        readyListener: function () {
            var question = questionsApp.question('custom-percentage-bar-response-1');

            updateScores(question);

            question.on('changed', function (r) {
                updateScores(question);
            });

            $('#validate_question').off().click(function() {
                questionsApp.validateQuestions();
            });
        }
    });

    function updateScores(question) {
        var score = question.getScore();
        $('#question_score').html((score && score.score) || 0);
        $('#question_max_score').html((score && score.max_score) || 0);
    }

</script>

<?php
    include_once 'views/modals/initialisation-preview.php';
    include_once 'includes/footer.php';
