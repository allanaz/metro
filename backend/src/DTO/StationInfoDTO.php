<?php

namespace App\DTO;

class StationDTO
{
    private string $code;
    private string $name;
    private ?string $stationTogether1;
    private string $lineCode1;
    private ?string $lineCode2;
    private ?string $lineCode3;

    public function __construct(
        string $code,
        string $name,
        ?string $stationTogether1,
        string $lineCode1,
        ?string $lineCode2,
        ?string $lineCode3
    ) {
        $this->code = $code;
        $this->name = $name;
        $this->stationTogether1 = $stationTogether1;
        $this->lineCode1 = $lineCode1;
        $this->lineCode2 = $lineCode2;
        $this->lineCode3 = $lineCode3;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['Code'],
            $data['Name'],
            $data['StationTogether1'] ?? null,
            $data['LineCode1'],
            $data['LineCode2'] ?? null,
            $data['LineCode3'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'name' => $this->name,
            'stationTogether1' => $this->stationTogether1,
            'lineCode1' => $this->lineCode1,
            'lineCode2' => $this->lineCode2,
            'lineCode3' => $this->lineCode3
        ];
    }

    // Getters
    public function getCode(): string
    {
        return $this->code;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getStationTogether1(): ?string
    {
        return $this->stationTogether1;
    }

    public function getLineCode1(): string
    {
        return $this->lineCode1;
    }

    public function getLineCode2(): ?string
    {
        return $this->lineCode2;
    }

    public function getLineCode3(): ?string
    {
        return $this->lineCode3;
    }
}

class StationInfoDTO
{
    /** @var StationDTO[] */
    private array $stations;

    /**
     * @param StationDTO[] $stations
     */
    public function __construct(array $stations)
    {
        $this->stations = $stations;
    }

    public static function fromApiResponse(array $data): self
    {
        $stations = array_map(
            function (array $stationData) {
                return StationDTO::fromArray($stationData);
            },
            $data['Stations'] ?? []
        );

        $names = array_map(function($station) {
            return $station->getName();
        }, $stations);

        array_multisort($names, SORT_ASC, $stations);        

        return new self($stations);
    }

    /**
     * @return array<int, array>
     */
    public function toArray(): array
    {
        return array_map(
            function (StationDTO $station) {
                return $station->toArray();
            },
            $this->stations
        );
    }

    /**
     * @return StationDTO[]
     */
    public function getStations(): array
    {
        return $this->stations;
    }
}