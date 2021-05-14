<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
    <link rel="stylesheet" href="./Debugger/templates/styles/common.css">
    <link rel="stylesheet" href="./Debugger/templates/styles/rainbow.css">
</head>
<body data-theme="blue">
    <div class="container">
        <div class="header">
            <div class="type-error"><?php echo $data->getType(); ?>:</div>
            <div class="wrapper__info-error">
                <div class="inner-header-info">
                    <span class="inner-bg-header">Error text &#8659;</span>
                    <div class="text-in-header">
                        <?php echo $data->getMessage(); ?>
                    </div>
                </div>
                <div class="inner-header-info">
                    <span class="inner-bg-header">Error file &#8658;</span>
                    <?php echo $data->getFile(); ?>
                </div>
                <div class="inner-header-info">
                    <span class="inner-bg-header">Error line &#8658;</span>
                    <?php echo $data->getLine(); ?>
                </div>
            </div>
        </div>
        <div class="container-tertiary">
            <?php
                foreach ($linesTemplate as $line => $value):
            ?>
                <div class="line-code <?php echo $line === $data->getLine() ? 'error' : '' ?>">
                    <span class="line-number"><?php echo $line; ?></span>
                    <span class="code-text language-html">
                        <pre><code><?php echo htmlspecialchars($value); ?></code></pre>
                    </span>
                </div>
            <?php
                endforeach;
            ?>
        </div>
    </div>
    <script src="./Debugger/templates/scripts/code-style.js"></script>
    <script>
        let maxWidthLine = 0;

        document.querySelectorAll('.line-code ').forEach(block => {
            const line = block.querySelector('.line-number');
            const widthLineNumber = line.getBoundingClientRect().width;

            if(widthLineNumber > maxWidthLine) {
                maxWidthLine = widthLineNumber;
            }

            hljs.highlightBlock(block.querySelector('.code-text'));
        });

        document.querySelectorAll('.line-number').forEach(block => {
            block.style.width = maxWidthLine + 'px';
        });
    </script>
</body>
</html>
