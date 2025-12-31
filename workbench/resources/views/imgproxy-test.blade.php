<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ImgProxy Laravel Package Test</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen py-8">
    <div class="container mx-auto px-4">
        <div class="max-w-6xl mx-auto">
            <h1 class="text-4xl font-bold text-center mb-8 text-gray-800">
                ImgProxy Laravel Package Test Suite
            </h1>

            <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
                <h2 class="text-2xl font-semibold mb-4 text-gray-700">Visual Image Processing Tests</h2>
                <p class="text-gray-600 mb-6">
                    Below are real-time examples of ImgProxy processing using the Laravel package.
                    All images are processed through your configured ImgProxy server.
                </p>

                <!-- Original Image -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium mb-3 text-gray-700">Original Image</h3>
                    <img src="https://picsum.photos/800/600" alt="Original" class="border rounded shadow-sm max-w-md">
                    <p class="text-sm text-gray-500 mt-2">Source: https://picsum.photos/800/600</p>
                </div>

                <!-- Basic Resizing -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium mb-3 text-gray-700">Basic Resizing (400x300)</h3>
                    <img src="{{ imgproxy('https://picsum.photos/800/600')->setWidth(400)->setHeight(300)->build() }}"
                         alt="Resized" class="border rounded shadow-sm">
                    <p class="text-sm text-gray-500 mt-2">
                        Code: <code class="bg-gray-100 px-1 rounded">imgproxy($url)->setWidth(400)->setHeight(300)->build()</code>
                    </p>
                </div>

                <!-- Quality Comparison -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium mb-3 text-gray-700">Quality Comparison</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <h4 class="font-medium mb-2">Low Quality (30%)</h4>
                            <img src="{{ imgproxy('https://picsum.photos/800/600')->setWidth(300)->setHeight(200)->setQuality(30)->build() }}"
                                 alt="Low Quality" class="border rounded shadow-sm w-full">
                        </div>
                        <div>
                            <h4 class="font-medium mb-2">Medium Quality (70%)</h4>
                            <img src="{{ imgproxy('https://picsum.photos/800/600')->setWidth(300)->setHeight(200)->setQuality(70)->build() }}"
                                 alt="Medium Quality" class="border rounded shadow-sm w-full">
                        </div>
                        <div>
                            <h4 class="font-medium mb-2">High Quality (95%)</h4>
                            <img src="{{ imgproxy('https://picsum.photos/800/600')->setWidth(300)->setHeight(200)->setQuality(95)->build() }}"
                                 alt="High Quality" class="border rounded shadow-sm w-full">
                        </div>
                    </div>
                </div>

                <!-- Format Comparison -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium mb-3 text-gray-700">Format Comparison</h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <h4 class="font-medium mb-2">JPEG</h4>
                            <img src="{{ imgproxy('https://picsum.photos/800/600')->setWidth(250)->setExtension(\Imsus\ImgProxy\Enums\OutputExtension::JPEG)->build() }}"
                                 alt="JPEG" class="border rounded shadow-sm w-full">
                        </div>
                        <div>
                            <h4 class="font-medium mb-2">PNG</h4>
                            <img src="{{ imgproxy('https://picsum.photos/800/600')->setWidth(250)->setExtension(\Imsus\ImgProxy\Enums\OutputExtension::PNG)->build() }}"
                                 alt="PNG" class="border rounded shadow-sm w-full">
                        </div>
                        <div>
                            <h4 class="font-medium mb-2">WebP</h4>
                            <img src="{{ imgproxy('https://picsum.photos/800/600')->setWidth(250)->setExtension(\Imsus\ImgProxy\Enums\OutputExtension::WEBP)->build() }}"
                                 alt="WebP" class="border rounded shadow-sm w-full">
                        </div>
                        <div>
                            <h4 class="font-medium mb-2">AVIF</h4>
                            <img src="{{ imgproxy('https://picsum.photos/800/600')->setWidth(250)->setExtension(\Imsus\ImgProxy\Enums\OutputExtension::AVIF)->build() }}"
                                 alt="AVIF" class="border rounded shadow-sm w-full">
                        </div>
                    </div>
                </div>

                <!-- Resize Types -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium mb-3 text-gray-700">Resize Types (300x200)</h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <h4 class="font-medium mb-2">Fit</h4>
                            <img src="{{ imgproxy('https://picsum.photos/800/600')->setWidth(300)->setHeight(200)->setResizeType(\Imsus\ImgProxy\Enums\ResizeType::FIT)->build() }}"
                                 alt="Fit" class="border rounded shadow-sm w-full">
                        </div>
                        <div>
                            <h4 class="font-medium mb-2">Fill</h4>
                            <img src="{{ imgproxy('https://picsum.photos/800/600')->setWidth(300)->setHeight(200)->setResizeType(\Imsus\ImgProxy\Enums\ResizeType::FILL)->build() }}"
                                 alt="Fill" class="border rounded shadow-sm w-full">
                        </div>
                        <div>
                            <h4 class="font-medium mb-2">Force</h4>
                            <img src="{{ imgproxy('https://picsum.photos/800/600')->setWidth(300)->setHeight(200)->setResizeType(\Imsus\ImgProxy\Enums\ResizeType::FORCE)->build() }}"
                                 alt="Force" class="border rounded shadow-sm w-full">
                        </div>
                        <div>
                            <h4 class="font-medium mb-2">Auto</h4>
                            <img src="{{ imgproxy('https://picsum.photos/800/600')->setWidth(300)->setHeight(200)->setResizeType(\Imsus\ImgProxy\Enums\ResizeType::AUTO)->build() }}"
                                 alt="Auto" class="border rounded shadow-sm w-full">
                        </div>
                    </div>
                </div>

                <!-- Visual Effects -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium mb-3 text-gray-700">Visual Effects</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <h4 class="font-medium mb-2">Blur Effect</h4>
                            <img src="{{ imgproxy('https://picsum.photos/800/600')->setWidth(300)->setBlur(2.0)->build() }}"
                                 alt="Blur" class="border rounded shadow-sm w-full">
                        </div>
                        <div>
                            <h4 class="font-medium mb-2">Sharpen Effect</h4>
                            <img src="{{ imgproxy('https://picsum.photos/800/600')->setWidth(300)->setSharpen(2.0)->build() }}"
                                 alt="Sharpen" class="border rounded shadow-sm w-full">
                        </div>
                        <div>
                            <h4 class="font-medium mb-2">High Saturation</h4>
                            <img src="{{ imgproxy('https://picsum.photos/800/600')->setWidth(300)->setSaturation(2.0)->build() }}"
                                 alt="Saturated" class="border rounded shadow-sm w-full">
                        </div>
                    </div>
                </div>

                <!-- Complex Processing -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium mb-3 text-gray-700">Complex Processing Pipeline</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <h4 class="font-medium mb-2">Portrait Enhancement</h4>
                            <img src="{{ imgproxy('https://picsum.photos/800/600')->setWidth(300)->setHeight(400)->setResizeType(\Imsus\ImgProxy\Enums\ResizeType::FILL)->setBrightness(10)->setContrast(1.1)->setSaturation(1.05)->setSharpen(0.8)->setQuality(92)->build() }}"
                                 alt="Enhanced Portrait" class="border rounded shadow-sm w-full">
                            <p class="text-xs text-gray-500 mt-1">Enhanced brightness, contrast, saturation, and sharpening</p>
                        </div>
                        <div>
                            <h4 class="font-medium mb-2">Vintage Effect</h4>
                            <img src="{{ imgproxy('https://picsum.photos/800/600')->setWidth(300)->setHeight(400)->setSaturation(0.7)->setContrast(0.9)->setBrightness(-10)->setQuality(85)->build() }}"
                                 alt="Vintage Effect" class="border rounded shadow-sm w-full">
                            <p class="text-xs text-gray-500 mt-1">Reduced saturation, lower contrast, and decreased brightness</p>
                        </div>
                    </div>
                </div>

                <!-- High DPI -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium mb-3 text-gray-700">High DPI Support</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <h4 class="font-medium mb-2">Standard DPI (1x)</h4>
                            <img src="{{ imgproxy('https://picsum.photos/800/600')->setWidth(200)->setHeight(150)->build() }}"
                                 alt="Standard DPI" class="border rounded shadow-sm">
                        </div>
                        <div>
                            <h4 class="font-medium mb-2">High DPI (2x)</h4>
                            <img src="{{ imgproxy('https://picsum.photos/800/600')->setWidth(200)->setHeight(150)->setDpr(2)->build() }}"
                                 alt="High DPI" class="border rounded shadow-sm" style="max-width: 200px;">
                        </div>
                    </div>
                </div>
            </div>

            <!-- API Test Links -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-2xl font-semibold mb-4 text-gray-700">API Test Endpoints</h2>
                <p class="text-gray-600 mb-4">
                    Click the links below to test the ImgProxy package API endpoints:
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <a href="/imgproxy-test/basic" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded block text-center transition">
                        Basic Test
                    </a>
                    <a href="/imgproxy-test/effects" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded block text-center transition">
                        Effects Test
                    </a>
                    <a href="/imgproxy-test/formats" class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded block text-center transition">
                        Formats Test
                    </a>
                    <a href="/imgproxy-test/resize" class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded block text-center transition">
                        Resize Test
                    </a>
                    <a href="/imgproxy-test/facade-vs-helper" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded block text-center transition">
                        Facade vs Helper
                    </a>
                    <a href="/imgproxy-test/config" class="bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2 rounded block text-center transition">
                        Config Test
                    </a>
                    <a href="/imgproxy-test/error-handling" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded block text-center transition">
                        Error Handling
                    </a>
                    <a href="/imgproxy-test/performance" class="bg-pink-500 hover:bg-pink-600 text-white px-4 py-2 rounded block text-center transition">
                        Performance Test
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
