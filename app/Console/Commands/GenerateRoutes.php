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
        $routes = $this->buildRoutesArray();
        $json   = $routes->toJson();

        file_put_contents(config('routes.target'), $json);

        $this->info(sprintf('Routes successfully generated in %s', config('routes.target')));
        $this->comment("Don't forget to rebuild assets using  npm run dev  or  npm run prod  !");

        return 0;
    }

    /**
     * Return a list of whitelisted routes as a Laravel Collection.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function buildRoutesArray()
    {
        $routes    = [];
        $whitelist = config('routes.whitelist');

        foreach ($this->router->getRoutes() as $route) {
            if (!in_array($route->getName(), $whitelist)) {
                continue;
            }

            $routes[$route->getName()] = $route->uri();
        }

        return collect($routes);
    }
}
