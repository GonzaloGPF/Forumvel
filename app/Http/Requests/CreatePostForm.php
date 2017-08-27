<?php

namespace App\Http\Requests;

use App\Exceptions\ThrottleAuthorization;
use App\Reply;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class CreatePostForm extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::allows('create', new Reply);
    }

    protected function failedAuthorization()
    {
        throw new ThrottleAuthorization('You are replying too frequently. Please take a break :)');
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'body' => 'required|spamfree'
        ];
    }

    public function persist($thread)
    {
        return $thread->addReply([
            'user_id' => auth()->id(),
            'body' => request('body')
        ])->load('owner');
    }
}
