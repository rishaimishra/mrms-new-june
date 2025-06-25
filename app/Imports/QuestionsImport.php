<?php

namespace App\Imports;

use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class QuestionsImport implements ToCollection
{
    /**
     * @param Collection $collection
     */
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $categories;

    public function __construct(Request $request)
    {
        $this->categories = $request->categories;

    }


    public function collection(Collection $rows)
    {
        /*row 0 Question
         * row 1 option A
         * row 2 option B
         * row 3 option C
         * row 4 option D
         * row 5 option E
         * row 6 Answer [A,B,C,D,E]
         * */
        foreach ($rows as $row) {

            if (isset($row[0]) && !empty($row[0])) {

                $question = trim($row[0]);
                $option_A = trim($row[1]);
                $option_B = trim($row[2]);
                $option_C = trim($row[3]);
                $option_D = trim($row[4]);
                $option_E = trim($row[5]);
                $answer = trim($row[6]);
                if($row[0] == "Question"){
                    continue;
                }

                if (!empty($question)) {
                    $createdQuestion = Question::create(['question' => $question]);
                    $createdQuestion->categories()->attach($this->categories);

                    if (isset($option_A) && !empty($option_A)) {
                        $option1 = ['option_value' => $option_A,
                            'question_id' => $createdQuestion->id,
                            'is_answer' => (($answer == $option_A) ? true : false)
                        ];
                        $createdQuestion->options()->create($option1);

                    }

                    if (isset($option_B) && !empty($option_B)) {
                        $option1 = ['option_value' => $option_B,
                            'question_id' => $createdQuestion->id,
                            'is_answer' => (($answer == $option_B) ? true : false)
                        ];
                        $createdQuestion->options()->create($option1);

                    }

                    if (isset($option_C) && !empty($option_C)) {
                        $option1 = ['option_value' => $option_C,
                            'question_id' => $createdQuestion->id,
                            'is_answer' => (($answer == $option_C) ? true : false)
                        ];
                        $createdQuestion->options()->create($option1);

                    }

                    if (isset($option_D) && !empty($option_D)) {
                        $option1 = ['option_value' => $option_D,
                            'question_id' => $createdQuestion->id,
                            'is_answer' => (($answer == $option_D) ? true : false)
                        ];
                        $createdQuestion->options()->create($option1);

                    }

                    if (isset($option_E) && !empty($option_E)) {
                        $option1 = ['option_value' => $option_E,
                            'question_id' => $createdQuestion->id,
                            'is_answer' => (($answer == $option_E) ? true : false)
                        ];
                        $createdQuestion->options()->create($option1);

                    }
                }
            }

        }
    }

}
