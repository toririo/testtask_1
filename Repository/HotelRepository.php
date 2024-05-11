<?php

class HotelRepository
{
    protected PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getHotelById(int $hotelId): bool|array
    {
        $query = <<<SQL
            SELECT * FROM hotels WHERE id = :hotelId
        SQL;
        $statement = $this->db->prepare($query);
        $statement->execute(['hotelId' => $hotelId]);
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function getHotelByIdForAgency(int $hotelId, int $agencyId): bool|array
    {
        $query = <<<SQL
            SELECT hotels.*, cities.country_id,
               agreements.discount_percent, agreements.comission_percent, agreements.is_default, agreements.company_id,
               options.agency_id, options.percent, options.is_black, options.is_recomend, options.is_white
            FROM hotels
            LEFT JOIN cities ON hotels.city_id = cities.id
            LEFT JOIN hotel_agreements AS agreements ON hotels.id = agreements.hotel_id
            LEFT JOIN agency_hotel_options AS options ON hotels.id = options.hotel_id
            WHERE hotels.id = :hotelId AND options.agency_id = :agencyId
        SQL;
        $statement = $this->db->prepare($query);
        $statement->execute(['hotelId' => $hotelId, 'agencyId' => $agencyId]);
        return $statement->fetch(PDO::FETCH_ASSOC);
    }
}
