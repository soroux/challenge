# Turpal - Technical Challenge

## Description

Imagine you are working for a travel company called _Travello_.

Travello started out by selling their own products (like experiences/tours/events) for a while. They became quite successful and they decided to integrate with 3rd party providers to expand their range of products.

Here you are expected to write a minimal working API for the business.

We have bootstrapped the project in a Laravel enviorment.
This is the initial [database schema](https://dbdiagram.io/d/630380baf1a9b01b0fbb25ca).

## Tasks

### 1. Search API

First you are expected to deliver Travello products through the search API:

-   Accessible by `GET /search` route.
-   Only **available** products should be shown.
-   Optionally filter by `startDate` and `endDate`. Default is 2 weeks from today.

Sample response structure:

```json
[
    {
        "title": "Desert Safari",
        "minimumPrice": "250.0 AED",
        "thumbnail": "https://picsum.photos/300/200"
    }
]
```

### 2. Integration

Now you are in charge of the integration. This is the primary focus of this challenge.

The idea is to have **transparent** integration with the ever-increasing list of providers.  
By transparent, we mean the Search API always presents the same response structure [as detailed above] for all sort of products.

#### Provider

Here is a fictional provider you need to integrate with:

1. Heavenly Tours ([API Docs](https://documenter.getpostman.com/view/24342027/2s8YekQEb6))

## Delivery

1. Clone this repository under your own git namespace.
2. Write us _your_ best code.
3. Send us the link to the repository.

We appreciate your time and effort in advance. Good luck!
