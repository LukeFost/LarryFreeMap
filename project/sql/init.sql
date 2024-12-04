-- Enable PostGIS extension first
CREATE EXTENSION IF NOT EXISTS postgis;

DO $$ 
BEGIN
    -- Create providers table if it doesn't exist
    IF NOT EXISTS (SELECT FROM pg_tables WHERE schemaname = 'public' AND tablename = 'providers') THEN
        CREATE TABLE providers (
            id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
            name TEXT NOT NULL,
            email TEXT,
            phone TEXT,
            created_at TIMESTAMPTZ DEFAULT NOW(),
            updated_at TIMESTAMPTZ DEFAULT NOW()
        );
        
        -- Create trigger for providers
        IF NOT EXISTS (SELECT 1 FROM pg_trigger WHERE tgname = 'set_timestamp_providers') THEN
            CREATE TRIGGER set_timestamp_providers
                BEFORE UPDATE ON providers
                FOR EACH ROW
                EXECUTE FUNCTION trigger_set_timestamp();
        END IF;
    END IF;

    -- Create buildings table if it doesn't exist
    IF NOT EXISTS (SELECT FROM pg_tables WHERE schemaname = 'public' AND tablename = 'buildings') THEN
        CREATE TABLE buildings (
            id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
            provider_id UUID REFERENCES providers(id),
            name TEXT NOT NULL,
            address TEXT NOT NULL,
            location GEOMETRY(Point, 4326),
            details JSONB,
            created_at TIMESTAMPTZ DEFAULT NOW(),
            updated_at TIMESTAMPTZ DEFAULT NOW()
        );
        
        -- Create trigger for buildings
        IF NOT EXISTS (SELECT 1 FROM pg_trigger WHERE tgname = 'set_timestamp_buildings') THEN
            CREATE TRIGGER set_timestamp_buildings
                BEFORE UPDATE ON buildings
                FOR EACH ROW
                EXECUTE FUNCTION trigger_set_timestamp();
        END IF;

        -- Create spatial index if it doesn't exist
        IF NOT EXISTS (SELECT 1 FROM pg_indexes WHERE indexname = 'idx_buildings_location') THEN
            CREATE INDEX idx_buildings_location ON buildings USING GIST (location);
        END IF;
    END IF;

    -- Create units table if it doesn't exist
    IF NOT EXISTS (SELECT FROM pg_tables WHERE schemaname = 'public' AND tablename = 'units') THEN
        CREATE TABLE units (
            id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
            building_id UUID REFERENCES buildings(id),
            unit_number TEXT NOT NULL,
            bedrooms INTEGER,
            bathrooms INTEGER,
            square_feet INTEGER,
            rent_amount DECIMAL(10,2),
            is_available BOOLEAN DEFAULT false,
            available_from DATE,
            features JSONB,
            created_at TIMESTAMPTZ DEFAULT NOW(),
            updated_at TIMESTAMPTZ DEFAULT NOW()
        );
        
        -- Create trigger for units
        IF NOT EXISTS (SELECT 1 FROM pg_trigger WHERE tgname = 'set_timestamp_units') THEN
            CREATE TRIGGER set_timestamp_units
                BEFORE UPDATE ON units
                FOR EACH ROW
                EXECUTE FUNCTION trigger_set_timestamp();
        END IF;
    END IF;

    -- Create or replace the timestamp trigger function
    CREATE OR REPLACE FUNCTION trigger_set_timestamp()
    RETURNS TRIGGER AS $$
    BEGIN
        NEW.updated_at = NOW();
        RETURN NEW;
    END;
    $$ LANGUAGE plpgsql;

EXCEPTION WHEN OTHERS THEN
    -- Log the error
    RAISE NOTICE 'Error creating tables: %', SQLERRM;
    -- Re-raise the error
    RAISE;
END $$;
