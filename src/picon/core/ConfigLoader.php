<?php

/**
 * Picon Framework
 * http://code.google.com/p/picon-framework/
 *
 * Copyright (C) 2011-2012 Martin Cassidy <martin.cassidy@webquub.com>

 * Picon Framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * Picon Framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with Picon Framework.  If not, see <http://www.gnu.org/licenses/>.
 * */

namespace picon;

/**
 * A helper class for loading config xml
 * @author Martin Cassidy
 * @package core
 * TODO add support for including other xml files allowing larger configs to be split up
 */
class ConfigLoader
{
	const SETTINGS_TAG_NAME = "settings";
	const PROFILES_TAG_NAME = "profile";
	const DATA_SOURCE_ELEMENT = "dataSource";

	private static $CORE_CONFIG = array("homePage", "mode");

	private function __construct()
	{
	}

	public static function load($file, &$config = null)
	{
		if($config==null)
		{
			$config = new Config();
		}

		$xml = new \DOMDocument();
		$xml->load($file);
		libxml_use_internal_errors(true);
		if (!$xml->schemaValidate(PICON_DIRECTORY.'/core/config.xsd'))
		{
			throw new ConfigException("Config XML does not match schema");
		}
		ConfigLoader::parse($xml, $config);
		return $config;
	}

	private static function parse(\DOMDocument $xml, Config &$config)
	{
		self::processSettings($xml, $config);
		self::processDataSources($xml, $config);
	}

	private static function processSettings(\DOMDocument $xml, Config &$config)
	{
		$settings = $xml->getElementsByTagName(self::SETTINGS_TAG_NAME)->item(0);
		$config->setHomePage($settings->getElementsByTagName("homePage")->item(0)->nodeValue);
		$profileName = $settings->getElementsByTagName("profile")->length==0?null:$settings->getElementsByTagName("profile")->item(0)->nodeValue;
		$config->setProfile($profileName==null?new ApplicationProfile():self::getProfile($xml, $profileName));
		$config->setStartup($settings->getElementsByTagName("startUp")->item(0)->nodeValue);
	}

	private static function getProfile(\DOMDocument $xml, $profileName)
	{
		$profiles = $xml->getElementsByTagName(self::PROFILES_TAG_NAME);

		foreach($profiles as $profile)
		{
			if($profile->getAttribute('name')==$profileName)
			{
				$aprofile = new ApplicationProfile();
				$values = array("showPiconTags" => Component::TYPE_BOOL, "cacheMarkup" => Component::TYPE_BOOL, "cleanBeforeOutput" => Component::TYPE_BOOL);
				foreach($values as $property => $type)
				{
					$value = $profile->getElementsByTagName($property)->item(0)->nodeValue;
					settype($value, $type);
					$aprofile->$property = $value;
				}
				return $aprofile;
			}
		}
		throw new ConfigException(sprintf("The %s profile name was not found.", $profileName));
	}

	private static function processDataSources(\DOMDocument $xml, Config &$config)
	{
		$sources = $xml->getElementsByTagName(self::DATA_SOURCE_ELEMENT);

		foreach($sources as $source)
		{
			$type = DataSourceType::valueOf($source->getAttribute('type'));
			$name = $source->getAttribute('name');
			$host = $source->getElementsByTagName('host')->item(0)->nodeValue;
			$port = $source->getElementsByTagName('port')->length==0?null:$source->getElementsByTagName('port')->item(0)->nodeValue;
			$username = $source->getElementsByTagName('username')->item(0)->nodeValue;
			$password = $source->getElementsByTagName('password')->item(0)->nodeValue;
			$database = $source->getElementsByTagName('database')->item(0)->nodeValue;
			$dataSource = new DataSourceConfig($type, $name, $host, $port, $username, $password, $database);
			$config->addDataSource($dataSource);
		}
	}
}

?>
