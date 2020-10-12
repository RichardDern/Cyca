<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Routing\Router;

class GenerateRoutes extends Command
{

    protected $router;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'route:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates a json file containing routes made available to frontend';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Router $router)
    {
        parent::__construct();

        $this->router = $router;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $routes    = [];
        $whitelist = config('routes.whitelist');

        foreach ($this->router->getRoutes() as $route) {
            if (!in_array($route->getName(), $whitelist)) {
                continue;
            }

            $routes[$route->getName()] = $route->uri();
        }

        $json = collect($routes)->toJson();

        file_put_contents(config('routes.target'), $json);

        return 0;
    }
}
