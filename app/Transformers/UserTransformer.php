<?php

namespace App\Transformers;

use Flugg\Responder\Transformers\Transformer;

class UserTransformer extends Transformer
{
    /**
     * Transform the data.
     *
     * @return array
     */
    public function transform($user, $type)
    {
        switch ($type) {
            case 'x':
                $item = [
                    'id'                => $user->parentIdentification,
                    'status'            => $this->mapStatus($user->statusCode, $type),
                    'balance'           => $user->parentAmount,
                    'currency'          => $user->Currency,
                    'registration_date' => date('Y-m-d', strtotime($user->registerationDate)),
                    'email'             => $user->parentEmail,
                ];
                break;
            case 'y':
                $item = [
                    'id' => $user->id,
                    'status'            => $this->mapStatus($user->status, $type),
                    'balance'           => $user->balance,
                    'currency'          => $user->currency,
                    'registration_date' => date('Y-m-d',
                        strtotime(str_replace('/', '-', $user->created_at))),
                    'email'             => $user->email,
                ];
                break;
        }
        return $item;
    }

    protected function mapStatus($status, $type): string
    {
        $statusMapping = [
            'x' => [
                1 => 'authorised',
                2 => 'decline',
                3 => 'refunded',
            ],
            'y' => [
                100 => 'authorised',
                200 => 'decline',
                300 => 'refunded',
            ]
        ];

        return $statusMapping[$type][$status] ?? 'invalid';
    }
}
