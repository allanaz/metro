# Allan's Metro Next Train App

I built this app using the template you provided, so the following instructions from the original repo still apply:

> **_NOTE:_** You will need to add a `WMATA_API_KEY` entry to one of the `.env` files first though.

To setup your local instance, run `./setup.sh`. This command will build the local docker images, install php and npm dependencies, and start the containers.

After that, you should be able to navigate to localhost:3000 and see the user interface I built. The API will be available at localhost:8000.

## Service Overview

Basically this app works like this:

WMATA API -> My API Service -> My Controller <-> Vue Frontend

Data comes from WMATA as json, gets processed into Dtos in my PHP layers, before being reserialized again into Json as it gets sent to the front end application. The PHP Dtos in `backend/src/DTO` match up to the typescript interfaces in `frontend/src/types/metro.ts`.  

As is, the user loads the website, picks a metro station out of an alphabetized list in the Select dropdown. Upon selection the frontend asks the backend for next trains to the station, including the connected station if its a transfer station (e.g. Metro Center, L'enfant Plaza). If the user picks a different station the process repeats for that station. 

With more time and to add more features I would add a database of some sort, allowing me to build in features like users, who could do things like log in and save their favorite stations. I could also store the station info in the db, and not deal with caching the WMATA stations endpoint. 

For testing it would be important to mock the WMATA api responses, and isolate the business logic responsible for transforming the data into whatever format necessary to provide the user experience we desire. We could write unit tests for the service and controller methods, and a smaller number of integration tests for key workflows. Eventually we could consider automation testing for the UI, allowing us to smoke test the entire application after deploys to various environments in our release process.  


