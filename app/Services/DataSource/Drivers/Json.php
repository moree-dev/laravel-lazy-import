<?php

declare(strict_types=1);

namespace App\Services\DataSource\Drivers;

use App\Exceptions\DataSourceException;
use App\Services\DataSource\DataSourceResult;
use Illuminate\Support\Facades\Log;
use JsonMachine\JsonMachine;

class Json extends Driver
{
    /**
     * @throws DataSourceException
     */
    public function read(string $path_to_file, int $offset, int $character_length): DataSourceResult
    {
        $partial_json_str = $this->readPart($path_to_file, $offset, $character_length);

        if (strlen($partial_json_str) === 0) {
            Log::alert(
                "got an empty result in reading file.",
                ['file' => $path_to_file, 'character_length' => $character_length, 'offset' => $offset]
            );
            return new DataSourceResult([], 0);
        }

        if($offset!==0)
            $partial_json_str[0] = '[';

        $json_result = JsonMachine::fromString($partial_json_str);

        $output_result = [];

        $position = 0;

        try {
            foreach ($json_result as $single_record) {
                array_push($output_result, $single_record);
                $position = $json_result->getPosition();
            }
        }catch(\Exception $e){
            if (count($output_result) === 0) {
                /**
                 * the $character_length value is not enough to retrieve even a single
                 * record from the json file.
                 */
                Log::error(
                    "the $character_length value is not enough to retrieve even a single * record from the json file",
                    ['file' => $path_to_file, 'character_length' => $character_length, 'offset' => $offset]
                );
                throw new DataSourceException(__('data_source.character_length_is_too_small.'), 104);
            } else {
                /**
                 * Ignoring the exception from JsonMachine because the last
                 * record might be incomplete and broken!
                 */
            }
        }

        return new DataSourceResult($output_result, $position);
    }
}
