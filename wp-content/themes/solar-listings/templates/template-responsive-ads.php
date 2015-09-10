<?php
/**
* Template Name: Responsive Ads
**/

$source = isset( $_GET['source'] ) ? '?source=' . $_GET['source'] : '';
$site   = isset( $_GET['site'] ) ? '&site=' . $_GET['site'] : '';
?>
<!DOCTYPE html>
<html lang="en-AU">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php wp_title(); ?></title>

    <!-- Bootstrap core CSS -->
    <link href='http://fonts.googleapis.com/css?family=Kreon:400,700' rel='stylesheet' type='text/css'>    
    <link href='http://fonts.googleapis.com/css?family=Roboto+Condensed:400,300' rel='stylesheet' type='text/css'>     
    <link href='http://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>

    <style type="text/css">
      	body{
          	background: #fff url('<?php echo get_stylesheet_directory_uri(); ?>/images/bg.png') repeat-x;
      	}
      	.container{
          	text-align: center;
          	min-height: 300px;
      	}
      	p{
        	font-family: 'Roboto Condensed', sans-serif;
          	font-weight: lighter;
          	font-size: 16px
     	}
        h1{
            text-transform: uppercase;
            color: #fc0;
            text-shadow: 2px 1px 2px #555;
        }
        h1 small{
              display: block;
              text-transform: none;
              color: #333;
              font-style: italic;
              font-size: 18px;
              font-weight: bold;
              text-shadow: none;
              font-family: 'Kreon', serif;
              margin-bottom: 8px;
        }
        a{
              display: block;
              font-size: 20px;
              padding: 10px 20px;
              color: #333;
              font-weight: bold;
              text-transform: uppercase;
              background: #fc0;
              border-bottom: 4px solid #ff9933;
              border-radius: 8px;
              text-decoration: none;
              max-width: 200px;
              margin: auto;
        }
        a:hover, a:active{
              color: #fff;
        }
          h1, a{
            font-family: 'Montserrat', sans-serif;
          }
          .col-xs-6{
              width: 50%;
              float: left;
          }
          .pull-right{
              float: right;
          }
          
          @media (max-width: 500px){
              .container{
                  background: none;
              }
              .col-xs-6{
                  width: 100%;
              }
              .col-xs-6.photo{
                display: none;
              }
          }
        @media (min-width: 501px) and (max-width: 650px){
            .col-xs-6{
                width: 58%;
            }
            .col-xs-6.photo{
                width: 40%;
            }
            .col-xs-6.photo img{
                max-width: 100%;
            }
        }
    </style>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    <div class="container">            
        <div class="row">
            <div class="col-xs-6 photo">
                <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/asq-calculator.png" alt="Calculate Savings" />
            </div>
            <div class="col-xs-6">
                <h1><small>Find out how much money you can save with</small> Solar Power</h1>
                <p class="description">Get up to $4000 off the cost of solar panels now whilst government
                incentives are still available. But hurry, the incentives won't last for ever!</p>
                <a href="https://www.australiansolarquotes.com.au/solar-quotes/<?php echo $source . $site ?>" target="_blank">Calculate Savings</a>
            </div>
        </div>
    </div>
  </body>
</html>
