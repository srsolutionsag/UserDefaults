<?php

namespace srag\Plugins\UserDefaults\API;

enum Commands: string
{
    case courses = "courses";
    case globalRoles = "globalRoles";
    case groups = "groups";
    case localRoles = "localRoles";
    case orgUnits = "orgUnits";

    case orgUnitPositions = "orgUnitPositions";
    case portfolioTemplates = "portfolioTemplates";
    case studyProgrammes = "studyProgrammes";
}
