<?php
require_once 'vendor/autoload.php';
require_once "./random_string.php";
use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;

//create coonection
$connectionString = "DefaultEndpointsProtocol=https;AccountName=lostfoundidwebapp;AccountKey=b6FQTpIrDcnavMWdB+1gmNy2TXC1qtHZNhDRu4x7Nw3SeQSHw0NlXh3JEQnEggQDAixU6mjgMxDqVEcIkmzo2A==;EndpointSuffix=core.windows.net";
$containerName = "lostfounfnewcontainer";
// Create blob client.
$blobClient = BlobRestProxy::createBlobService($connectionString);

//if submit file button is clicked
if (isset($_POST['submit'])) {
	$fileToUpload = strtolower($_FILES["fileToUpload"]["name"]);
	$content = fopen($_FILES["fileToUpload"]["tmp_name"], "r");
	// 
	$blobClient->createBlockBlob($containerName, $fileToUpload, $content);
}
$listBlobsOptions = new ListBlobsOptions();
$listBlobsOptions->setPrefix("");
$result = $blobClient->listBlobs($containerName, $listBlobsOptions);

//if url in table is clicked
if (isset($_POST['analyze'])) {
	if (isset($_POST['url'])) {
		$url = $_POST['url'];
	}
} else {
	$url = "Klik Link pada tabel";
}
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
	    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	    <title>Submit Storage & Analyze</title>
	    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	    <!-- <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script> -->
	</head>
	<body>
		<div >
		    <ul>
		        <li><a href="../index.php">Home Input</a></li>
		        <li><a href="#">Storage And Computer Vision</a></li>
		    </ul>
		</div>
		<div  class="container">		
			<h1>Pelaporan Barang Hilang dan Temuan Barang</h1>
			<p class="lead">Pilih Gambar Barang.</p>

			<div >
				<form  action="" method="post" enctype="multipart/form-data">
					<input type="file" name="fileToUpload" accept=".jpeg,.jpg,.png" required="">
					<input type="submit" name="submit" value="Upload">
				</form>
			</div>
			<br>
			<br>
			<table >
				<thead>
					<tr>
						<td>Nama File</td>
						<td>URL (Klik untuk auto-copy ke field analyze computer vision)</td>
					</tr>
				</thead>
				<tbody>
					<?php
					do {
						foreach ($result->getBlobs() as $blob)
						{
							?>
							<tr>
								<td><?php echo $blob->getName() ?></td>
								<td>
								<form action="" method="post">
									<input type="hidden" name="url" value="<?php echo $blob->getUrl()?>">
									<input type="submit" name="analyze" value="<?php echo $blob->getUrl() ?>" >
								</form>
								</td>
								<!-- <td><?php echo $blob->getUrl() ?></td> -->
		
							</tr>
							<?php
						}
						$listBlobsOptions->setContinuationToken($result->getContinuationToken());
					} while($result->getContinuationToken());
					?>
				</tbody>
			</table>
		</div>
		<br><br>
		<hr width="70%" align="left">
		<hr width="70%" align="left">

		<script type="text/javascript">
		    function processImage() {
		        var subscriptionKey = "bdf9d07cedca4a69a9f3f643aaebe034";
		        // Free trial subscription keys are generated in the "westus" region.
		        // If you use a free trial subscription key, you shouldn't need to change
		        // this region.
		        var uriBase =
		             "https://southeastasia.api.cognitive.microsoft.com/vision/v2.0/analyze";;
		        // Request parameters.
		        var params = {
		            "visualFeatures": "Categories,Description,Color",
		            "details": "",
		            "language": "en",
		        };
		        // Display the image.
		        var sourceImageUrl = document.getElementById("inputImage").value;
		        document.querySelector("#sourceImage").src = sourceImageUrl;
		        // Make the REST API call.
		        $.ajax({
		            url: uriBase + "?" + $.param(params),
		            // Request headers.
		            beforeSend: function(xhrObj){
		                xhrObj.setRequestHeader("Content-Type","application/json");
		                xhrObj.setRequestHeader(
		                    "Ocp-Apim-Subscription-Key", subscriptionKey);
		            },
		            type: "POST",
		            // Request body.
		            data: '{"url": ' + '"' + sourceImageUrl + '"}',
		        })
		        .done(function(data) {
		            // Show formatted JSON on webpage.
		            $("#responseTextArea").val(JSON.stringify(data, null, 2));
		        })
		        .fail(function(jqXHR, textStatus, errorThrown) {
		            // Display error message.
		            var errorString = (errorThrown === "") ? "Error. " :
		                errorThrown + " (" + jqXHR.status + "): ";
		            errorString += (jqXHR.responseText === "") ? "" :
		                jQuery.parseJSON(jqXHR.responseText).message;
		            alert(errorString);
		        });
		    };
		</script>

		<h1>Analyze image:</h1>
		Enter the URL to an image, then click the <strong>Analyze image</strong> button.
		<br><br>
		Image to analyze:
		<input type="text" name="inputImage" id="inputImage" value="<?php echo $url ?>" />
		<button onclick="processImage()">Analyze image</button>
		<br><br>
		<div id="wrapper" style="width:1020px; display:table;">
		    <div id="jsonOutput" style="width:600px; display:table-cell;">
		        Response:
		        <br><br>
		        <textarea id="responseTextArea" class="UIInput"
		                  style="width:580px; height:400px;"></textarea>
		    </div>
		    <div id="imageDiv" style="width:420px; display:table-cell;">
		        Source image:
		        <br><br>
		        <img id="sourceImage" width="400" />
		    </div>
		</div>
	</body>

</html>