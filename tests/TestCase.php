<?php

namespace OrangeShadow\Polls\Test;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use OrangeShadow\Polls\Test\Models\User;
use Illuminate\Database\Schema\Blueprint;
use OrangeShadow\Polls\Poll;
use OrangeShadow\Polls\Option;

class TestCase extends OrchestraTestCase
{

    protected $pollSingle;
    protected $pollMulti;
    protected $pollVariable;
    protected $user;

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();

        $this->artisan('migrate', [
            '--database' => 'testbench',
            '--realpath' => realpath(__DIR__.'/src/migrations'),
        ]);

        $this->setUpDatabase();

    }

    protected function getPackageProviders($app)
    {
        return ['OrangeShadow\Polls\ServiceProvider'];
    }


    protected function seedPolls()
    {
        $this->pollSingle = Poll::create([
            'title'     => 'Who is the best football player in the world?',
            'active'    => 1,
            'anonymous' => 0,
            'position'  => 1,
            'type'      => 'OrangeShadow\\Polls\\Types\\SingleVote',
        ]);

        Option::create([
            'title'    => 'Messi',
            'poll_id'  => $this->pollSingle->id,
            'position' => 1
        ]);

        Option::create([
            'title'    => 'Ronaldo',
            'poll_id'  => $this->pollSingle->id,
            'position' => 2
        ]);

        Option::create([
            'title'    => 'Neymar',
            'poll_id'  => $this->pollSingle->id,
            'position' => 3
        ]);


        $this->pollMulti = Poll::create([
            'title'     => 'What programming language do you use in your job?',
            'active'    => 1,
            'anonymous' => 0,
            'position'  => 2,
            'type'      => 'OrangeShadow\\Polls\\Types\\MultiVote',
        ]);

        Option::create([
            'title'    => 'PHP',
            'poll_id'  => $this->pollMulti->id,
            'position' => 1
        ]);

        Option::create([
            'title'    => 'Nodejs',
            'poll_id'  => $this->pollMulti->id,
            'position' => 2
        ]);

        Option::create([
            'title'    => 'Python',
            'poll_id'  => $this->pollMulti->id,
            'position' => 3
        ]);

        Option::create([
            'title'    => 'Java',
            'poll_id'  => $this->pollMulti->id,
            'position' => 4
        ]);


        $this->pollVariable = Poll::create([
            'title'     => 'How Do Developers Assess Potential Jobs?',
            'active'    => 1,
            'anonymous' => 0,
            'position'  => 2,
            'type'      => 'OrangeShadow\\Polls\\Types\\VariableVote',
        ]);

        Option::create([
            'title'    => 'The compensation and benefits offered',
            'poll_id'  => $this->pollVariable->id,
            'position' => 5
        ]);

        Option::create([
            'title'    => 'The languages, frameworks, and other technologies I\'d be working with',
            'poll_id'  => $this->pollVariable->id,
            'position' => 6
        ]);

        Option::create([
            'title'    => 'Opportunities for professional development',
            'poll_id'  => $this->pollVariable->id,
            'position' => 2
        ]);

        Option::create([
            'title'    => 'The office environment or company culture',
            'poll_id'  => $this->pollVariable->id,
            'position' => 4
        ]);

        Option::create([
            'title'    => 'The opportunity to work from home/remotely',
            'poll_id'  => $this->pollVariable->id,
            'position' => 3
        ]);

        Option::create([
            'title'    => 'The industry that I\'d be working in',
            'poll_id'  => $this->pollVariable->id,
            'position' => 1
        ]);

        $this->user = User::find(1);
    }


    protected function getPackageAliases($app)
    {
        return [
            'PollWriter' => 'OrangeShadow\Polls\PollWriterFacade'
        ];
    }


    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        $app['config']->set('app.key', '6rE9Nz59bGRbeMATftriyQjrpF7DcOQm');

        $app['config']->set('polls',include __DIR__.'/../src/config/polls.php');
    }

    public function getTempDirectory(): string
    {
        return __DIR__.'/temp';
    }

    protected function setUpDatabase()
    {
        $this->resetDatabase();
        $this->createTables('users');
        $this->seedModels(User::class);
    }
    protected function resetDatabase()
    {
        file_put_contents($this->getTempDirectory().'/database.sqlite', null);
    }

    protected function createTables(...$tableNames)
    {
        collect($tableNames)->each(function (string $tableName) {
            $this->app['db']->connection()->getSchemaBuilder()->create($tableName, function (Blueprint $table) use ($tableName) {
                $table->increments('id');
                $table->string('name')->nullable();
                $table->string('text')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        });
    }

    protected function seedModels(...$modelClasses)
    {
        collect($modelClasses)->each(function (string $modelClass) {
            foreach (range(2, 0) as $index) {
                $modelClass::create(['name' => "name {$index}"]);
            }
        });
    }
}