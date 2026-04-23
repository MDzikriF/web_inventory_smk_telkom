<!DOCTYPE html>
<html>
<head>
    <title>Test Image Display</title>
</head>
<body>
    <h1>Image Display Test</h1>
    
    <h2>Test Storage Link</h2>
    <p>Storage link exists: <?php echo file_exists(public_path('storage')) ? 'YES' : 'NO'; ?></p>
    <p>Storage link target: <?php echo readlink(public_path('storage')) ?: 'Not a symlink'; ?></p>
    
    <h2>Test Item Images</h2>
    <?php
    $itemImages = glob(public_path('storage/items/*.{jpg,jpeg,png,gif}'), GLOB_BRACE);
    foreach ($itemImages as $image) {
        $filename = basename($image);
        echo "<div style='margin: 10px 0;'>";
        echo "<h4>$filename</h4>";
        echo "<img src='/storage/items/$filename' style='max-width: 200px; height: auto; border: 1px solid #ccc;' onerror='this.style.border=\"2px solid red\"; this.alt=\"Failed to load\";' alt='Test image'>";
        echo "</div>";
    }
    ?>
    
    <h2>Test Chat Images</h2>
    <?php
    $chatImages = glob(public_path('storage/chat_files/*.{jpg,jpeg,png,gif}'), GLOB_BRACE);
    foreach ($chatImages as $image) {
        $filename = basename($image);
        echo "<div style='margin: 10px 0;'>";
        echo "<h4>$filename</h4>";
        echo "<img src='/storage/chat_files/$filename' style='max-width: 200px; height: auto; border: 1px solid #ccc;' onerror='this.style.border=\"2px solid red\"; this.alt=\"Failed to load\";' alt='Test image'>";
        echo "</div>";
    }
    ?>
    
    <h2>Test Asset Helper</h2>
    <p>asset('/storage/items/3CZuO3q6AujfmECcGAqWOi3LcDZft4c2LfA3ODQo.jpg'): 
        <img src="<?php echo asset('storage/items/3CZuO3q6AujfmECcGAqWOi3LcDZft4c2LfA3ODQo.jpg'); ?>" style='max-width: 100px; height: auto;' onerror='this.style.border=\"2px solid red\";' alt='Asset test'>
    </p>
    
    <h2>File Permissions</h2>
    <pre>
<?php
    $storagePath = public_path('storage');
    echo "Storage directory: $storagePath\n";
    echo "Readable: " . (is_readable($storagePath) ? 'YES' : 'NO') . "\n";
    echo "Writable: " . (is_writable($storagePath) ? 'YES' : 'NO') . "\n";
    
    $itemsPath = public_path('storage/items');
    echo "Items directory: $itemsPath\n";
    echo "Readable: " . (is_readable($itemsPath) ? 'YES' : 'NO') . "\n";
    
    $testFile = public_path('storage/items/3CZuO3q6AujfmECcGAqWOi3LcDZft4c2LfA3ODQo.jpg');
    echo "Test file: $testFile\n";
    echo "Exists: " . (file_exists($testFile) ? 'YES' : 'NO') . "\n";
    echo "Readable: " . (is_readable($testFile) ? 'YES' : 'NO') . "\n";
    echo "Size: " . (file_exists($testFile) ? filesize($testFile) : 'N/A') . " bytes\n";
?>
    </pre>
</body>
</html>
