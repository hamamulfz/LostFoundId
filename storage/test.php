  # Mengatur instance dari Azure::Storage::Client
    $connectionString = "DefaultEndpointsProtocol=https;AccountName=".getenv('account_name').";AccountKey=".getenv('account_key');
 
    // Membuat blob client.
    $blobClient = BlobRestProxy::createBlobService($connectionString);
 
    # Membuat BlobService yang merepresentasikan Blob service untuk storage account
    $createContainerOptions = new CreateContainerOptions();
 
    $createContainerOptions->setPublicAccess(PublicAccessType::CONTAINER_AND_BLOBS);
 
    // Menetapkan metadata dari container.
    $createContainerOptions->addMetaData("key1", "value1");
    $createContainerOptions->addMetaData("key2", "value2");
 
    $containerName = "blockblobs".generateRandomString();
 
    try    {
        // Membuat container.
        $blobClient->createContainer($containerName, $createContainerOptions);
 
 
    // Sampai kode di atas kita telah membuat instancce Azure storage client, menginstansiasi objek blob service, membuat container baru, dan mengatur perijinan ke container agar blob bisa diakses oleh semua.