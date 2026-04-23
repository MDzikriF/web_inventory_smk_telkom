<!DOCTYPE html>
<html>
<head>
    <title>Simple Image Test</title>
</head>
<body>
    <h1>Testing Image Display</h1>
    
    <h2>Direct Unsplash URL:</h2>
    <img src="https://images.unsplash.com/photo-1547036967-23d11aacaee0?w=300&h=300&fit=crop" width="100" height="100" style="border: 2px solid red;">
    
    <h2>Placeholder:</h2>
    <img src="https://via.placeholder.com/100x100.png?text=Test" width="100" height="100" style="border: 2px solid green;">
    
    <h2>Test with item data from database:</h2>
    <?php
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    $items = \App\Models\Item::take(2)->get();
    foreach ($items as $item) {
        echo "<p><strong>{$item->name}</strong><br>";
        echo "Photo: " . ($item->photo ?? 'NULL') . "<br>";
        if ($item->photo) {
            echo "<img src='{$item->photo}' width='100' height='100' style='border: 2px solid blue;' alt='{$item->name}'>";
        }
        echo "</p><hr>";
    }
    ?>
    
    <script>
        document.querySelectorAll('img').forEach((img, index) => {
            img.onload = function() {
                console.log('Image ' + index + ' loaded:', this.src);
            };
            img.onerror = function() {
                console.error('Image ' + index + ' failed:', this.src);
                this.style.border = '2px solid red';
                this.alt = 'FAILED: ' + this.src;
            };
        });
    </script>
</body>
</html>
