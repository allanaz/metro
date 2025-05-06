<?php

namespace App\DTO;

class TrainDTO
{
    private string $length;
    private string $destination;
    private ?string $line;
    private string $arrival;

    public function __construct(string $length, string $destination, ?string $line, string $arrival)
    {
        $this->length = $length;
        $this->destination = $destination;
        $this->line = $line;
        $this->arrival = $arrival;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['Car'] ?? '---',
            $data['Destination'] ?? 'unknown',
            $data['Line'] ?? null,
            $data['Min'] ?? ''
        );
    }

    public function toArray(): array
    {
        return [
            'length' => $this->length,
            'destination' => $this->destination,
            'line' => $this->line,
            'arrival' => $this->arrival
        ];
    }

    // Getters
    public function getLength(): string
    {
        return $this->length;
    }

    public function getDestination(): string
    {
        return $this->destination;
    }

    public function getLine(): ?string
    {
        return $this->line;
    }

    public function getArrival(): string
    {
        return $this->arrival;
    }
}

class TrainInfoDTO
{
    /** @var TrainDTO[] */
    private array $trains;

    /**
     * @param TrainDTO[] $trains
     */
    public function __construct(array $trains)
    {
        $this->trains = $trains;
    }

    public static function fromApiResponse(array $data): self
    {
        $trains = array_map(
            function (array $trainData) {
                return TrainDTO::fromArray($trainData);
            },
            $data['Trains'] ?? []
        );

        return new self($trains);
    }

    /**
     * @return array<int, array>
     */
    public function toArray(): array
    {
        return array_map(
            function (TrainDTO $train) {
                return $train->toArray();
            },
            $this->trains
        );
    }

    /**
     * @return TrainDTO[]
     */
    public function getTrains(): array
    {
        return $this->trains;
    }
}