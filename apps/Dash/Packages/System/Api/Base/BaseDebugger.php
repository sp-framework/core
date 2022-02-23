<?php

namespace Apps\Dash\Packages\System\Api\Base;

class BaseDebugger
{
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config + [
            'logfn' => function ($msg) {
                echo $msg.PHP_EOL;
            },
            'scrub_credentials' => true,
            'scrub_strings' => []
        ];

        $this->config['scrub_strings'] += self::$credentialsStrings;
    }

    /**
     * @param string $info The debug information.
     */
    public function __invoke($info)
    {
        if ($this->config['scrub_credentials']) {
            foreach ($this->config['scrub_strings'] as $pattern => $replacement) {
                $info = preg_replace($pattern, $replacement, $info);
            }
        }
        $this->config['logfn']($info);
    }
}