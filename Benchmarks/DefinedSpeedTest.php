<?php namespace Benchmarks;

use Closure;
use Tests\Fakes\Projectors\NoModeProjector;
use Tests\Fakes\Projectors\RunOnce;

/**
 * Benchmarks for the defined function in PHP
 *
 * THis function is reported as being very slow here
 * http://ie1.php.net/manual/en/function.defined.php#89886
 *
 * This was 8 years ago. How well does it work in PHP7?
 *
 * Conclusion:
 * Checking a defined const is around ~2.1 times slower than a simple bool check
 * Checking an undefined const is around ~1.7 times slower than a simple bool check
 *
 * Big changes from the last benchmark. No real slow down, perfectly fine to use it as is.
 */
class DefinedSpeedTest extends \PHPUnit_Framework_TestCase
{
    public function test_speed_of_running_check()
    {
        $bool_total = $this->runFunction1000Times(function(){
            return true;
        });
        var_dump("Simple true: $bool_total");

        $defined_total = $this->runFunction1000Times(function(){
            return defined(RunOnce::class."::MODE");
        });
        $defined_ratio = round($defined_total/$bool_total, 1);
        var_dump("Defined const: $defined_total ($defined_ratio)");

        $undefined_total = $this->runFunction1000Times(function(){
           return defined(NoModeProjector::class."::MODE");
        });
        $undefined_ratio = round($undefined_total/$bool_total, 1);
        var_dump("Undefined const: $undefined_total ($undefined_ratio)");
    }

    private function runFunction1000Times(Closure $func): float
    {
        $total = 0;
        for ($i = 0; $i < 1000; $i++) {
            $start = microtime(true);
            if ($func()) {
                $end = microtime(true);
            } else {
                $end = microtime(true);
            }

            $total += $end - $start;
        }
        return $total;
    }
}